<?php
/**
 * Model Attachment - Anexos de projetos e tarefas
 *
 * Relacionamento polimórfico: pode pertencer a um projeto
 * ou a uma tarefa via entity_type/entity_id.
 * Arquivos armazenados no MinIO.
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property string $filename
 * @property string $original_name
 * @property string $mime_type
 * @property int $size
 * @property string $storage_path
 * @property int $uploaded_by
 * @property int $created_at
 *
 * @property User $uploader
 *
 * @package app\models
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

class Attachment extends ActiveRecord
{
    /** @var UploadedFile Arquivo para upload */
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attachment}}';
    }

    /**
     * Behaviors: apenas created_at.
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * Regras de validação.
     */
    public function rules()
    {
        return [
            [['entity_type', 'entity_id'], 'required'],
            ['entity_type', 'in', 'range' => ['project', 'task']],
            [['entity_id', 'uploaded_by', 'size'], 'integer'],

            ['filename', 'string', 'max' => 255],
            ['original_name', 'string', 'max' => 255],
            ['mime_type', 'string', 'max' => 100],
            ['storage_path', 'string', 'max' => 500],

            // Validação do arquivo de upload
            ['file', 'file',
                'extensions' => Yii::$app->params['allowedFileTypes'] ?? ['pdf', 'png', 'jpg', 'jpeg', 'gif'],
                'mimeTypes' => Yii::$app->params['allowedMimeTypes'] ?? [],
                'maxSize' => Yii::$app->params['maxUploadSize'] ?? 10 * 1024 * 1024,
                'wrongExtension' => 'Extensão não permitida. Use: {extensions}',
                'wrongMimeType' => 'Tipo de arquivo não permitido.',
                'tooBig' => 'Arquivo muito grande. Máximo: {formattedLimit}',
            ],
        ];
    }

    /**
     * Labels amigáveis.
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Arquivo',
            'original_name' => 'Nome do Arquivo',
            'mime_type' => 'Tipo',
            'size' => 'Tamanho',
            'created_at' => 'Enviado em',
        ];
    }

    // =============================================
    // Relacionamentos
    // =============================================

    /**
     * Quem fez o upload.
     * @return \yii\db\ActiveQuery
     */
    public function getUploader()
    {
        return $this->hasOne(User::class, ['id' => 'uploaded_by']);
    }

    // =============================================
    // Upload
    // =============================================

    /**
     * Faz upload do arquivo para o MinIO e salva o model.
     *
     * @param UploadedFile $uploadedFile
     * @param string $entityType (project ou task)
     * @param int $entityId
     * @return bool
     */
    public function uploadFile(UploadedFile $uploadedFile, string $entityType, int $entityId): bool
    {
        // Gerar nome seguro único (UUID + extensão original)
        $extension = strtolower($uploadedFile->extension);
        $uniqueName = Yii::$app->security->generateRandomString(32) . '.' . $extension;
        $storagePath = "{$entityType}/{$entityId}/{$uniqueName}";

        try {
            // Upload para MinIO
            Yii::$app->minio->upload(
                $uploadedFile->tempName,
                $storagePath,
                $uploadedFile->type
            );

            // Preencher model
            $this->entity_type = $entityType;
            $this->entity_id = $entityId;
            $this->filename = $uniqueName;
            $this->original_name = $uploadedFile->baseName . '.' . $extension;
            $this->mime_type = $uploadedFile->type;
            $this->size = $uploadedFile->size;
            $this->storage_path = $storagePath;
            $this->uploaded_by = Yii::$app->user->id;

            return $this->save();
        } catch (\Exception $e) {
            Yii::error("Erro ao fazer upload: " . $e->getMessage(), __METHOD__);
            $this->addError('file', 'Falha ao enviar arquivo. Tente novamente.');
            return false;
        }
    }

    /**
     * Retorna URL pré-assinada para download.
     *
     * @param int $expiry Minutos de validade
     * @return string
     */
    public function getDownloadUrl(int $expiry = 60): string
    {
        return Yii::$app->minio->getPresignedUrl($this->storage_path, $expiry);
    }

    /**
     * Retorna URL pública do arquivo.
     * @return string
     */
    public function getPublicUrl(): string
    {
        return Yii::$app->minio->getUrl($this->storage_path);
    }

    /**
     * Verifica se o arquivo é uma imagem.
     * @return bool
     */
    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/png', 'image/jpeg', 'image/gif']);
    }

    /**
     * Retorna tamanho formatado (KB, MB).
     * @return string
     */
    public function getFormattedSize(): string
    {
        if ($this->size >= 1048576) {
            return round($this->size / 1048576, 1) . ' MB';
        }
        return round($this->size / 1024, 1) . ' KB';
    }

    /**
     * Ao deletar, remove o arquivo do MinIO.
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->minio->delete($this->storage_path);
    }
}
