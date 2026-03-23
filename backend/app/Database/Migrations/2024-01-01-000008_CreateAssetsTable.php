<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'campaign_id' => ['type' => 'INT', 'unsigned' => true],
            'offer_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'uploaded_by' => ['type' => 'INT', 'unsigned' => true],
            'file_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 500],
            'file_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'file_size' => ['type' => 'INT', 'unsigned' => true],
            'asset_type' => [
                'type' => 'ENUM',
                'constraint' => ['briefing', 'content', 'revision'],
                'default' => 'content',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending_review', 'approved', 'revision_requested', 'replaced'],
                'default' => 'pending_review',
            ],
            'reviewed_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'reviewed_at' => ['type' => 'DATETIME', 'null' => true],
            'review_notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('assets');
    }

    public function down()
    {
        $this->forge->dropTable('assets');
    }
}
