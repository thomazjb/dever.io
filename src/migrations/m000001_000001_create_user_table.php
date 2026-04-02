<?php
/**
 * Migration: Criação da tabela `user`
 *
 * Armazena dados de usuários do sistema com autenticação
 * via email/senha e suporte a "remember me" via auth_key.
 */

use yii\db\Migration;

class m000001_000001_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull()->comment('Nome completo do usuário'),
            'email' => $this->string(255)->notNull()->unique()->comment('Email único para login'),
            'password_hash' => $this->string(255)->notNull()->comment('Hash bcrypt da senha'),
            'auth_key' => $this->string(64)->notNull()->comment('Chave de autenticação para cookies'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment('10=ativo, 0=inativo'),
            'created_at' => $this->integer()->notNull()->comment('Timestamp de criação'),
            'updated_at' => $this->integer()->notNull()->comment('Timestamp de atualização'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->createIndex('idx-user-email', '{{%user}}', 'email', true);
        $this->createIndex('idx-user-status', '{{%user}}', 'status');
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
