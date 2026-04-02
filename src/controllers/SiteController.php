<?php
/**
 * SiteController - Páginas gerais do sistema
 *
 * @package app\controllers
 */

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Página inicial - redireciona para dashboard ou login.
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['/auth/login']);
        }
        return $this->redirect(['/dashboard/index']);
    }
}
