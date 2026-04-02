<?php
/**
 * Model RegisterForm - Formulário de registro de usuário
 *
 * Valida dados de cadastro, cria hash da senha e
 * persiste novo usuário no banco de dados.
 *
 * @package app\models
 */

namespace app\models;

use Yii;
use yii\base\Model;

class RegisterForm extends Model
{
    /** @var string Nome completo */
    public $name;
    /** @var string Email */
    public $email;
    /** @var string Senha */
    public $password;
    /** @var string Confirmação de senha */
    public $password_confirm;

    /**
     * Regras de validação.
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'password_confirm'], 'required', 'message' => '{attribute} é obrigatório'],
            ['name', 'string', 'min' => 2, 'max' => 255],
            ['name', 'trim'],
            ['email', 'email', 'message' => 'Email inválido'],
            ['email', 'trim'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Este email já está cadastrado'],
            ['password', 'string', 'min' => 6, 'tooShort' => 'A senha deve ter no mínimo 6 caracteres'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'As senhas não conferem'],
        ];
    }

    /**
     * Labels amigáveis.
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Nome Completo',
            'email' => 'Email',
            'password' => 'Senha',
            'password_confirm' => 'Confirmar Senha',
        ];
    }

    /**
     * Registra um novo usuário no sistema.
     *
     * @return User|null Usuário criado ou null em caso de falha
     */
    public function register(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if ($user->save()) {
            return $user;
        }

        // Transferir erros do model para o form
        foreach ($user->errors as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->addError($attribute, $error);
            }
        }

        return null;
    }
}
