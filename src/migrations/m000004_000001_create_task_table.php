<?php
/**
 * Migration: Criação da tabela `task`
 *
 * Armazena tarefas vinculadas a projetos com prioridade,
 * status, data de vencimento e atribuição a usuário.
 */

use yii\db\Migration;

class m000004_000001_create_task_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey()->unsigned(),
            'project_id' => $this->integer()->unsigned()->notNull()->comment('Projeto ao qual pertence'),
            'assigned_to' => $this->integer()->unsigned()->null()->comment('Usuário responsável'),
            'title' => $this->string(255)->notNull()->comment('Título da tarefa'),
            'description' => $this->text()->null()->comment('Descrição detalhada'),
            'due_date' => $this->date()->null()->comment('Data de vencimento'),
            'priority' => $this->string(10)->notNull()->defaultValue('medium')->comment('low, medium, high'),
            'status' => $this->string(20)->notNull()->defaultValue('pending')->comment('pending, in_progress, completed'),
            'completed_at' => $this->integer()->null()->comment('Timestamp de conclusão'),
            'created_by' => $this->integer()->unsigned()->notNull()->comment('Quem criou a tarefa'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addForeignKey(
            'fk-task-project_id',
            '{{%task}}',
            'project_id',
            '{{%project}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-task-assigned_to',
            '{{%task}}',
            'assigned_to',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-task-created_by',
            '{{%task}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-task-project_id', '{{%task}}', 'project_id');
        $this->createIndex('idx-task-assigned_to', '{{%task}}', 'assigned_to');
        $this->createIndex('idx-task-status', '{{%task}}', 'status');
        $this->createIndex('idx-task-priority', '{{%task}}', 'priority');
        $this->createIndex('idx-task-due_date', '{{%task}}', 'due_date');
    }

    public function safeDown()
    {
        $this->dropTable('{{%task}}');
    }
}
