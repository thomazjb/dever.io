<?php
/**
 * Configuração do banco de dados MySQL
 *
 * Utiliza variáveis de ambiente para conexão,
 * permitindo configuração via Docker Compose.
 */
return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf(
        'mysql:host=%s;dbname=%s',
        getenv('DB_HOST') ?: 'localhost',
        getenv('DB_NAME') ?: 'dever_db'
    ),
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4',

    // Schema cache para melhor performance em produção
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
