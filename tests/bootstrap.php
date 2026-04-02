<?php
/**
 * Bootstrap dos testes - Inicializa ambiente Yii2 para testes
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Incluir TestCase manualmente
require __DIR__ . '/TestCase.php';

// Configuração de teste baseada na config web
$config = require __DIR__ . '/config.php';

// Criar instância da aplicação (sem rodar)
new yii\web\Application($config);
