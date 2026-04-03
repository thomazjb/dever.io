<?php
/**
 * AuthController - Autenticação de usuários
 *
 * Gerencia login, registro e logout de usuários.
 * Acessível apenas para visitantes (exceto logout).
 *
 * @package app\controllers
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;

class AuthController extends Controller
{
    /**
     * Controle de acesso: login e registro apenas para visitantes.
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'register'],
                        'allow' => true,
                        'roles' => ['?'], // Apenas visitantes
                    ],
                    [
                        'actions' => ['logout', 'profile', 'update-profile'],
                        'allow' => true,
                        'roles' => ['@'], // Apenas autenticados
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Login de usuário.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $this->layout = 'main';
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'Bem-vindo ao Dever.io!');
            return $this->redirect(['/dashboard/index']);
        }

        $this->view->title = 'Entrar';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Registro de novo usuário.
     *
     * @return string|\yii\web\Response
     */
    public function actionRegister()
    {
        $this->layout = 'main';
        $model = new RegisterForm();

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->register();
            if ($user !== null) {
                // Login automático após registro
                Yii::$app->user->login($user, 0);
                Yii::$app->session->setFlash('success', 'Conta criada com sucesso! Bem-vindo ao Dever.io!');
                return $this->redirect(['/dashboard/index']);
            }
        }

        $this->view->title = 'Criar Conta';
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Exibe e processa edição de perfil.
     *
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        $this->layout = 'main';
        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;
        $user->scenario = 'update';

        if ($user->load(Yii::$app->request->post()) && $user->validate()) {
            if (!empty($user->password)) {
                $user->setPassword($user->password);
            }

            if ($user->save(false)) {
                Yii::$app->session->setFlash('success', 'Perfil atualizado com sucesso!');
                return $this->refresh();
            }
        }

        $this->view->title = 'Meu Perfil';
        return $this->render('profile', ['model' => $user]);
    }

    /**
     * Logout do usuário.
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['/auth/login']);
    }
}
