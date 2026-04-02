<?php
/**
 * Entry point da aplicação web Yii2 - Dever.io
 */

// Modo debug e ambiente (dev para desenvolvimento)
defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG') !== 'false');
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV') ?: 'dev');

// Autoload do Composer
require __DIR__ . '/../vendor/autoload.php';

// Framework Yii2
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Carregar configuração
$config = require __DIR__ . '/../config/web.php';

// Criar e executar aplicação
(new yii\web\Application($config))->run();
