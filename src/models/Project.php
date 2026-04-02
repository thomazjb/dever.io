<?php
/**
 * Model Project - Modelo de projeto
 *
 * Gerencia projetos com título, descrição, datas e relacionamentos
 * com usuários (dono e membros) e tarefas.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property int $owner_id
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $owner Usuário dono do projeto
 * @property User[] $members Membros do projeto
 * @property Task[] $tasks Tarefas do projeto
 * @property Attachment[] $attachments Anexos do projeto
 *
 * @package app\models
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Project extends ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%project}}';
    }

    /**
     * Behaviors: timestamp automático.
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Regras de validação.
     */
    public function rules()
    {
        return [
            [['title'], 'required', 'message' => 'Título é obrigatório'],
            ['title', 'string', 'max' => 255],
            ['title', 'trim'],

            ['description', 'string'],
            ['description', 'default', 'value' => null],

            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Data inválida'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>=',
                'message' => 'Data de conclusão deve ser após a data de início',
                'when' => function ($model) {
                    return !empty($model->start_date) && !empty($model->end_date);
                }
            ],

            ['owner_id', 'integer'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_ARCHIVED]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    /**
     * Antes de salvar, define status padrão se estiver vazio.
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (empty($this->status)) {
            $this->status = self::STATUS_ACTIVE;
        }

        return true;
    }

    /**
     * Labels amigáveis.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Título',
            'description' => 'Descrição',
            'start_date' => 'Data de Início',
            'end_date' => 'Data de Conclusão',
            'owner_id' => 'Responsável',
            'status' => 'Status',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    // =============================================
    // Relacionamentos
    // =============================================

    /**
     * Dono do projeto.
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    /**
     * Membros do projeto (via tabela pivô).
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('{{%project_user}}', ['project_id' => 'id']);
    }

    /**
     * Registros da tabela pivô project_user.
     * @return \yii\db\ActiveQuery
     */
    public function getProjectUsers()
    {
        return $this->hasMany(ProjectUser::class, ['project_id' => 'id']);
    }

    /**
     * Tarefas do projeto.
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['project_id' => 'id']);
    }

    /**
     * Anexos do projeto (relação polimórfica).
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(Attachment::class, ['entity_id' => 'id'])
            ->andWhere(['entity_type' => 'project']);
    }

    // =============================================
    // Métodos de autorização
    // =============================================

    /**
     * Verifica se o usuário é dono do projeto.
     *
     * @param int|null $userId ID do usuário (null = usuário logado)
     * @return bool
     */
    public function isOwner(?int $userId = null): bool
    {
        $userId = $userId ?? Yii::$app->user->id;
        return (int) $this->owner_id === (int) $userId;
    }

    /**
     * Verifica se o usuário é membro do projeto.
     *
     * @param int|null $userId ID do usuário (null = usuário logado)
     * @return bool
     */
    public function isMember(?int $userId = null): bool
    {
        $userId = $userId ?? Yii::$app->user->id;
        return ProjectUser::find()
            ->where(['project_id' => $this->id, 'user_id' => $userId])
            ->exists();
    }

    /**
     * Verifica se o usuário tem acesso ao projeto (dono ou membro).
     *
     * @param int|null $userId
     * @return bool
     */
    public function hasAccess(?int $userId = null): bool
    {
        return $this->isOwner($userId) || $this->isMember($userId);
    }

    /**
     * Adiciona um membro ao projeto.
     *
     * @param int $userId
     * @param string $role
     * @return bool
     */
    public function addMember(int $userId, string $role = 'member'): bool
    {
        if ($this->isMember($userId) || $this->isOwner($userId)) {
            return false; // Já é membro ou dono
        }

        $pivot = new ProjectUser();
        $pivot->project_id = $this->id;
        $pivot->user_id = $userId;
        $pivot->role = $role;

        return $pivot->save();
    }

    /**
     * Remove um membro do projeto.
     *
     * @param int $userId
     * @return bool
     */
    public function removeMember(int $userId): bool
    {
        if ($this->isOwner($userId)) {
            return false; // Não pode remover o dono
        }

        return ProjectUser::deleteAll([
            'project_id' => $this->id,
            'user_id' => $userId,
        ]) > 0;
    }

    // =============================================
    // Métodos auxiliares
    // =============================================

    /**
     * Conta tarefas por status.
     *
     * @param string $status
     * @return int
     */
    public function getTaskCount(string $status = ''): int
    {
        $query = $this->getTasks();
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        return $query->count();
    }

    /**
     * Calcula o percentual de progresso do projeto.
     *
     * @return float
     */
    public function getProgress(): float
    {
        $total = $this->getTaskCount();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->getTaskCount('completed');
        return round(($completed / $total) * 100, 1);
    }

    /**
     * Após salvar um novo projeto, adiciona o dono como membro.
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $pivot = new ProjectUser();
            $pivot->project_id = $this->id;
            $pivot->user_id = $this->owner_id;
            $pivot->role = 'owner';
            $pivot->save();
        }
    }
}
