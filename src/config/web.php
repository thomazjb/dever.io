<?php
/**
 * Configuração principal da aplicação web Yii2 - Dever.io
 *
 * Este arquivo configura todos os componentes, módulos e parâmetros
 * da aplicação incluindo banco de dados, sessão, URLs e MinIO.
 */

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'dever-io',
    'name' => 'Dever.io',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'pt-BR',
    'defaultRoute' => 'dashboard/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        // Componente de requisição HTTP
        'request' => [
            'cookieValidationKey' => 'dever-io-secret-cookie-key-change-in-production',
            'enableCsrfValidation' => true,
        ],

        // Cache em arquivo
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        // Componente de autenticação de usuário
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['auth/login'],
        ],

        // Tratamento de erros
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        // Logs da aplicação
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        // Conexão com o banco de dados
        'db' => $db,

        // Gerenciamento de URLs amigáveis
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Autenticação
                'login' => 'auth/login',
                'register' => 'auth/register',
                'logout' => 'auth/logout',

                // Dashboard
                'dashboard' => 'dashboard/index',

                // Projetos
                'projects' => 'project/index',
                'project/create' => 'project/create',
                'project/<id:\d+>' => 'project/view',
                'project/<id:\d+>/edit' => 'project/update',
                'project/<id:\d+>/delete' => 'project/delete',
                'project/<id:\d+>/members' => 'project/members',
                'project/<id:\d+>/add-member' => 'project/add-member',
                'project/<id:\d+>/remove-member' => 'project/remove-member',

                // Tarefas
                'my-tasks' => 'task/my-tasks',
                'project/<projectId:\d+>/tasks' => 'task/index',
                'project/<projectId:\d+>/task/create' => 'task/create',
                'project/<projectId:\d+>/task/<id:\d+>' => 'task/view',
                'project/<projectId:\d+>/task/<id:\d+>/edit' => 'task/update',
                'project/<projectId:\d+>/task/<id:\d+>/delete' => 'task/delete',
                'project/<projectId:\d+>/task/<id:\d+>/complete' => 'task/complete',

                // Remoção de anexos
                'project/delete-attachment/<id:\d+>' => 'project/delete-attachment',
                'task/delete-attachment/<id:\d+>' => 'task/delete-attachment',
            ],
        ],

        // Componente MinIO (S3 compatible)
        'minio' => [
            'class' => 'app\components\MinioComponent',
        ],

        // Sessão
        'session' => [
            'class' => 'yii\web\DbSession',
            'sessionTable' => 'session',
        ],
    ],
    'params' => $params,
];

return $config;
