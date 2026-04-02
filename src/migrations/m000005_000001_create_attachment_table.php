<?php
/**
 * Migration: Criação da tabela `attachment`
 *
 * Armazena referências de arquivos enviados (PDF, imagens)
 * com relacionamento polimórfico (pode pertencer a project ou task).
 */

use yii\db\Migration;

class m000005_000001_create_attachment_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%attachment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'entity_type' => $this->string(20)->notNull()->comment('Tipo: project ou task'),
            'entity_id' => $this->integer()->unsigned()->notNull()->comment('ID da entidade'),
            'filename' => $this->string(255)->notNull()->comment('Nome único no storage'),
            'original_name' => $this->string(255)->notNull()->comment('Nome original do arquivo'),
            'mime_type' => $this->string(100)->notNull()->comment('Tipo MIME do arquivo'),
            'size' => $this->integer()->unsigned()->notNull()->comment('Tamanho em bytes'),
            'storage_path' => $this->string(500)->notNull()->comment('Caminho completo no MinIO'),
            'uploaded_by' => $this->integer()->unsigned()->notNull()->comment('Usuário que fez upload'),
            'created_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addForeignKey(
            'fk-attachment-uploaded_by',
            '{{%attachment}}',
            'uploaded_by',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-attachment-entity', '{{%attachment}}', ['entity_type', 'entity_id']);
        $this->createIndex('idx-attachment-uploaded_by', '{{%attachment}}', 'uploaded_by');
    }

    public function safeDown()
    {
        $this->dropTable('{{%attachment}}');
    }
}
