<?php
/**
 * Configuração para ambiente de testes
 *
 * Usa SQLite em memória para testes rápidos sem depender do MySQL.
 */

$params = require __DIR__ . '/../config/params.php';

return [
    'id' => 'dever-io-tests',
    'basePath' => dirname(__DIR__),
    'language' => 'pt-BR',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite::memory:',
            'charset' => 'utf8',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'request' => [
            'cookieValidationKey' => 'test-secret-key',
            'enableCsrfValidation' => false,
            'scriptFile' => __DIR__ . '/index.php',
            'scriptUrl' => '/index.php',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'minio' => [
            'class' => 'app\components\MinioComponent',
        ],
        'security' => [
            'class' => 'yii\base\Security',
        ],
        'session' => [
            'class' => 'yii\web\Session',
        ],
    ],
    'params' => $params,
];
