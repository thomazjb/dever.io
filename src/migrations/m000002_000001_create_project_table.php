<?php
/**
 * Migration: Criação da tabela `project`
 *
 * Armazena projetos com título, descrição, datas e
 * referência ao usuário proprietário (owner).
 */

use yii\db\Migration;

class m000002_000001_create_project_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%project}}', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(255)->notNull()->comment('Título do projeto'),
            'description' => $this->text()->null()->comment('Descrição detalhada'),
            'start_date' => $this->date()->null()->comment('Data de início'),
            'end_date' => $this->date()->null()->comment('Data prevista de conclusão'),
            'owner_id' => $this->integer()->unsigned()->notNull()->comment('Usuário dono do projeto'),
            'status' => $this->string(20)->notNull()->defaultValue('active')->comment('active, archived'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addForeignKey(
            'fk-project-owner_id',
            '{{%project}}',
            'owner_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-project-owner_id', '{{%project}}', 'owner_id');
        $this->createIndex('idx-project-status', '{{%project}}', 'status');
    }

    public function safeDown()
    {
        $this->dropTable('{{%project}}');
    }
}
