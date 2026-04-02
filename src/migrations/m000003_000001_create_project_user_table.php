<?php
/**
 * Migration: Criação da tabela `project_user` (N:N)
 *
 * Tabela pivô para relacionamento muitos-para-muitos
 * entre usuários e projetos, com papel do membro.
 */

use yii\db\Migration;

class m000003_000001_create_project_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%project_user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'project_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'role' => $this->string(20)->notNull()->defaultValue('member')->comment('owner, member'),
            'created_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addForeignKey(
            'fk-project_user-project_id',
            '{{%project_user}}',
            'project_id',
            '{{%project}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-project_user-user_id',
            '{{%project_user}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Índice único para evitar duplicatas
        $this->createIndex(
            'idx-project_user-unique',
            '{{%project_user}}',
            ['project_id', 'user_id'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%project_user}}');
    }
}
