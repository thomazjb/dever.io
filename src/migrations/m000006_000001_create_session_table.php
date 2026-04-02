<?php
/**
 * Migration: Criação da tabela `session` para DbSession do Yii2
 */

use yii\db\Migration;

class m000006_000001_create_session_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%session}}', [
            'id' => $this->char(64)->notNull(),
            'expire' => $this->integer()->notNull(),
            'data' => $this->binary(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addPrimaryKey('pk-session', '{{%session}}', 'id');
        $this->createIndex('idx-session-expire', '{{%session}}', 'expire');
    }

    public function safeDown()
    {
        $this->dropTable('{{%session}}');
    }
}
