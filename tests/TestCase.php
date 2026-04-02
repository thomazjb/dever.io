<?php
/**
 * TestCase base para testes unitários
 *
 * Fornece helpers de criação de tabelas em SQLite
 * e setup/teardown do ambiente Yii2.
 */

namespace tests;

use Yii;
use yii\db\Connection;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Configura ambiente antes de cada teste.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }

    /**
     * Limpa ambiente após cada teste.
     */
    protected function tearDown(): void
    {
        $this->dropTables();
        parent::tearDown();
    }

    /**
     * Cria tabelas em SQLite para testes.
     */
    protected function createTables(): void
    {
        $db = Yii::$app->db;

        // Tabela user
        $db->createCommand()->createTable('user', [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'name' => 'VARCHAR(255) NOT NULL',
            'email' => 'VARCHAR(255) NOT NULL UNIQUE',
            'password_hash' => 'VARCHAR(255) NOT NULL',
            'auth_key' => 'VARCHAR(64) NOT NULL',
            'status' => 'SMALLINT NOT NULL DEFAULT 10',
            'created_at' => 'INT NOT NULL DEFAULT 0',
            'updated_at' => 'INT NOT NULL DEFAULT 0',
        ])->execute();

        // Tabela project
        $db->createCommand()->createTable('project', [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'title' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'start_date' => 'DATE',
            'end_date' => 'DATE',
            'owner_id' => 'INT NOT NULL',
            'status' => 'VARCHAR(20) NOT NULL DEFAULT "active"',
            'created_at' => 'INT NOT NULL DEFAULT 0',
            'updated_at' => 'INT NOT NULL DEFAULT 0',
        ])->execute();

        // Tabela project_user
        $db->createCommand()->createTable('project_user', [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'project_id' => 'INT NOT NULL',
            'user_id' => 'INT NOT NULL',
            'role' => 'VARCHAR(20) NOT NULL DEFAULT "member"',
            'created_at' => 'INT NOT NULL DEFAULT 0',
        ])->execute();

        // Tabela task
        $db->createCommand()->createTable('task', [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'project_id' => 'INT NOT NULL',
            'assigned_to' => 'INT',
            'title' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'due_date' => 'DATE',
            'priority' => 'VARCHAR(10) NOT NULL DEFAULT "medium"',
            'status' => 'VARCHAR(20) NOT NULL DEFAULT "pending"',
            'completed_at' => 'INT',
            'created_by' => 'INT NOT NULL',
            'created_at' => 'INT NOT NULL DEFAULT 0',
            'updated_at' => 'INT NOT NULL DEFAULT 0',
        ])->execute();

        // Tabela attachment
        $db->createCommand()->createTable('attachment', [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'entity_type' => 'VARCHAR(20) NOT NULL',
            'entity_id' => 'INT NOT NULL',
            'filename' => 'VARCHAR(255) NOT NULL',
            'original_name' => 'VARCHAR(255) NOT NULL',
            'mime_type' => 'VARCHAR(100) NOT NULL',
            'size' => 'INT NOT NULL DEFAULT 0',
            'storage_path' => 'VARCHAR(500) NOT NULL',
            'uploaded_by' => 'INT NOT NULL',
            'created_at' => 'INT NOT NULL DEFAULT 0',
        ])->execute();
    }

    /**
     * Remove todas as tabelas após o teste.
     */
    protected function dropTables(): void
    {
        $db = Yii::$app->db;
        foreach (['attachment', 'task', 'project_user', 'project', 'user'] as $table) {
            try {
                $db->createCommand()->dropTable($table)->execute();
            } catch (\Exception $e) {
                // Ignora se tabela não existir
            }
        }
    }

    /**
     * Cria um usuário de teste.
     */
    protected function createTestUser(array $attributes = []): \app\models\User
    {
        $user = new \app\models\User();
        $user->name = $attributes['name'] ?? 'Usuário Teste';
        $user->email = $attributes['email'] ?? 'teste@dever.io';
        $user->setPassword($attributes['password'] ?? 'senha123');
        $user->generateAuthKey();
        $user->status = \app\models\User::STATUS_ACTIVE;
        $user->save(false);
        return $user;
    }

    /**
     * Cria um projeto de teste associado a um usuário.
     */
    protected function createTestProject(\app\models\User $owner, array $attributes = []): \app\models\Project
    {
        $project = new \app\models\Project();
        $project->title = $attributes['title'] ?? 'Projeto Teste';
        $project->description = $attributes['description'] ?? 'Descrição do projeto de teste';
        $project->owner_id = $owner->id;
        $project->save(false);

        // Simular afterSave (adicionar dono como membro)
        $pivot = new \app\models\ProjectUser();
        $pivot->project_id = $project->id;
        $pivot->user_id = $owner->id;
        $pivot->role = 'owner';
        $pivot->save(false);

        return $project;
    }
}
