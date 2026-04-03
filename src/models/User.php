<?php
/**
 * Model User - Modelo de usuário com autenticação
 *
 * Implementa IdentityInterface do Yii2 para suporte completo
 * a login, sessão e "remember me". Inclui validações de dados
 * e métodos auxiliares de senha.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Project[] $ownedProjects Projetos que o usuário é dono
 * @property Project[] $projects Projetos dos quais é membro
 * @property Task[] $assignedTasks Tarefas atribuídas ao usuário
 *
 * @package app\models
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

class User extends ActiveRecord implements IdentityInterface
{
    /** Campo temporário para senha modificada */
    public $password;

    /** Campo temporário para confirmação de senha */
    public $password_repeat;

    /** Status: conta ativa */
    const STATUS_ACTIVE = 10;
    /** Status: conta inativa */
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Behaviors: timestamp automático em created_at/updated_at.
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Regras de validação dos atributos.
     */
    public function rules()
    {
        return [
            ['name', 'required', 'message' => 'Nome é obrigatório'],
            ['name', 'string', 'min' => 2, 'max' => 255],
            ['name', 'trim'],

            ['email', 'required', 'message' => 'Email é obrigatório'],
            ['email', 'email', 'message' => 'Email inválido'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => self::class, 'targetAttribute' => 'email',
                'filter' => function ($query) {
                    if (!$this->isNewRecord) {
                        $query->andWhere(['<>', 'id', $this->id]);
                    }
                },
                'message' => 'Este email já está cadastrado',
            ],
            ['email', 'trim'],

            ['password', 'string', 'min' => 8, 'skipOnEmpty' => true,
                'message' => 'A senha deve ter no mínimo 8 caracteres.'],

            ['password_repeat', 'required', 'when' => function ($model) {
                return !empty($model->password);
            }, 'whenClient' => "function (attribute, value) { return $('#user-password').val().length > 0; }", 'message' => 'Por favor confirme a nova senha.'],

            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'As senhas não coincidem.'],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
        ];
    }

    /**
     * Labels amigáveis para exibição em formulários.
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_DEFAULT] = ['name', 'email', 'status', 'password', 'password_repeat'];
        $scenarios['update'] = ['name', 'email', 'password', 'password_repeat'];

        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'email' => 'Email',
            'password_hash' => 'Senha',
            'password' => 'Nova senha',
            'status' => 'Status',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    // =============================================
    // Implementação de IdentityInterface
    // =============================================

    /**
     * Encontra usuário pelo ID.
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Encontra usuário pelo token de acesso (não utilizado neste projeto).
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Encontra usuário pelo email.
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // =============================================
    // Métodos de senha
    // =============================================

    /**
     * Valida a senha informada contra o hash armazenado.
     *
     * @param string $password Senha em texto puro
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Gera hash da senha e armazena.
     *
     * @param string $password Senha em texto puro
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Gera um novo auth_key aleatório.
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    // =============================================
    // Relacionamentos
    // =============================================

    /**
     * Projetos dos quais o usuário é dono.
     * @return \yii\db\ActiveQuery
     */
    public function getOwnedProjects()
    {
        return $this->hasMany(Project::class, ['owner_id' => 'id']);
    }

    /**
     * Projetos dos quais o usuário é membro (via tabela pivô).
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'project_id'])
            ->viaTable('{{%project_user}}', ['user_id' => 'id']);
    }

    /**
     * Tarefas atribuídas ao usuário.
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedTasks()
    {
        return $this->hasMany(Task::class, ['assigned_to' => 'id']);
    }

    /**
     * Retorna as iniciais do nome do usuário (para avatar).
     * @return string
     */
    public function getInitials(): string
    {
        $parts = explode(' ', $this->name);
        if (count($parts) >= 2) {
            return strtoupper($parts[0][0] . $parts[count($parts) - 1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
