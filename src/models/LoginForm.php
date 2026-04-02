<?php
/**
 * Model LoginForm - Formulário de login
 *
 * Valida credenciais do usuário (email + senha) e
 * realiza autenticação na sessão do Yii2.
 *
 * @package app\models
 */

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    /** @var string Email do usuário */
    public $email;
    /** @var string Senha do usuário */
    public $password;
    /** @var bool Manter logado */
    public $rememberMe = true;

    /** @var User|null Cache do usuário encontrado */
    private $_user;

    /**
     * Regras de validação.
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required', 'message' => '{attribute} é obrigatório'],
            ['email', 'email', 'message' => 'Email inválido'],
            ['email', 'trim'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Labels dos campos.
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Senha',
            'rememberMe' => 'Manter-me conectado',
        ];
    }

    /**
     * Validação customizada da senha contra o banco de dados.
     *
     * @param string $attribute
     * @param array $params
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Email ou senha incorretos.');
            }
        }
    }

    /**
     * Realiza o login do usuário.
     *
     * @return bool Sucesso do login
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? 3600 * 24 * 30 : 0 // 30 dias
            );
        }
        return false;
    }

    /**
     * Busca o usuário pelo email informado.
     *
     * @return User|null
     */
    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }
        return $this->_user;
    }
}
