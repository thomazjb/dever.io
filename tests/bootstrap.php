<?php
/**
 * Bootstrap dos testes - Inicializa ambiente Yii2 para testes
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__ . '/../src/vendor/autoload.php';
require __DIR__ . '/../src/vendor/yiisoft/yii2/Yii.php';

// Configuração de teste baseada na config web
$config = require __DIR__ . '/config.php';

// Criar instância da aplicação (sem rodar)
new yii\web\Application($config);
