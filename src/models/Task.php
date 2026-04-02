<?php
/**
 * Model Task - Modelo de tarefa
 *
 * Gerencia tarefas vinculadas a projetos com prioridade,
 * status, data de vencimento e atribuição a membro.
 *
 * @property int $id
 * @property int $project_id
 * @property int|null $assigned_to
 * @property string $title
 * @property string $description
 * @property string $due_date
 * @property string $priority
 * @property string $status
 * @property int|null $completed_at
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Project $project
 * @property User $assignee
 * @property User $creator
 * @property Attachment[] $attachments
 *
 * @package app\models
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Task extends ActiveRecord
{
    // Status possíveis
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    // Prioridades
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%task}}';
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
            [['title', 'project_id'], 'required', 'message' => '{attribute} é obrigatório'],
            ['title', 'string', 'max' => 255],
            ['title', 'trim'],

            ['description', 'string'],
            ['description', 'default', 'value' => null],

            ['due_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Data inválida'],

            ['priority', 'in', 'range' => [self::PRIORITY_LOW, self::PRIORITY_MEDIUM, self::PRIORITY_HIGH]],
            ['priority', 'default', 'value' => self::PRIORITY_MEDIUM],

            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED]],
            ['status', 'default', 'value' => self::STATUS_PENDING],

            [['project_id', 'assigned_to', 'created_by', 'completed_at'], 'integer'],

            ['project_id', 'exist', 'targetClass' => Project::class, 'targetAttribute' => 'id'],
            ['assigned_to', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * Labels amigáveis.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Projeto',
            'assigned_to' => 'Responsável',
            'title' => 'Título',
            'description' => 'Descrição',
            'due_date' => 'Data de Vencimento',
            'priority' => 'Prioridade',
            'status' => 'Status',
            'completed_at' => 'Concluída em',
            'created_by' => 'Criado por',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    // =============================================
    // Relacionamentos
    // =============================================

    /**
     * Projeto ao qual a tarefa pertence.
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * Usuário responsável pela tarefa.
     * @return \yii\db\ActiveQuery
     */
    public function getAssignee()
    {
        return $this->hasOne(User::class, ['id' => 'assigned_to']);
    }

    /**
     * Usuário que criou a tarefa.
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Anexos da tarefa (relação polimórfica).
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(Attachment::class, ['entity_id' => 'id'])
            ->andWhere(['entity_type' => 'task']);
    }

    // =============================================
    // Métodos de status
    // =============================================

    /**
     * Marca a tarefa como concluída.
     * @return bool
     */
    public function markAsCompleted(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = time();
        return $this->save(false, ['status', 'completed_at', 'updated_at']);
    }

    /**
     * Verifica se a tarefa está atrasada.
     * @return bool
     */
    public function isOverdue(): bool
    {
        if ($this->status === self::STATUS_COMPLETED || empty($this->due_date)) {
            return false;
        }
        return strtotime($this->due_date) < strtotime('today');
    }

    /**
     * Verifica se a tarefa está concluída.
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Retorna texto formatado da prioridade.
     * @return string
     */
    public function getPriorityLabel(): string
    {
        $labels = Yii::$app->params['taskPriorities'] ?? [];
        return $labels[$this->priority] ?? $this->priority;
    }

    /**
     * Retorna texto formatado do status.
     * @return string
     */
    public function getStatusLabel(): string
    {
        $labels = Yii::$app->params['taskStatuses'] ?? [];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Retorna cor CSS associada à prioridade.
     * @return string
     */
    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH => 'red',
            self::PRIORITY_MEDIUM => 'amber',
            self::PRIORITY_LOW => 'emerald',
            default => 'slate',
        };
    }

    /**
     * Retorna cor CSS associada ao status.
     * @return string
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'emerald',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_PENDING => 'amber',
            default => 'slate',
        };
    }

    // =============================================
    // Scopes / Queries
    // =============================================

    /**
     * Tarefas pendentes (não concluídas).
     * @return \yii\db\ActiveQuery
     */
    public static function findPending()
    {
        return static::find()->where(['!=', 'status', self::STATUS_COMPLETED]);
    }

    /**
     * Tarefas atrasadas.
     * @return \yii\db\ActiveQuery
     */
    public static function findOverdue()
    {
        return static::find()
            ->where(['!=', 'status', self::STATUS_COMPLETED])
            ->andWhere(['<', 'due_date', date('Y-m-d')])
            ->andWhere(['is not', 'due_date', null]);
    }
}
