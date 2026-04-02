<?php
/**
 * Model ProjectUser - Tabela pivô projeto-usuário
 *
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property string $role
 * @property int $created_at
 *
 * @package app\models
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class ProjectUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%project_user}}';
    }

    /**
     * Behaviors: apenas created_at (sem updated_at).
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
            [['project_id', 'user_id'], 'required'],
            [['project_id', 'user_id'], 'integer'],
            ['role', 'in', 'range' => ['owner', 'member']],
            ['role', 'default', 'value' => 'member'],
            [['project_id', 'user_id'], 'unique', 'targetAttribute' => ['project_id', 'user_id'],
                'message' => 'Usuário já é membro deste projeto'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
