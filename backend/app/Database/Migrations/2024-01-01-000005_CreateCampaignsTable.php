<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCampaignsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'brand_id' => ['type' => 'INT', 'unsigned' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'budget' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'target_audience' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'countries' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'start_date' => ['type' => 'DATE'],
            'end_date' => ['type' => 'DATE'],
            'briefing_file' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'pending_approval', 'approved', 'active', 'completed', 'cancelled'],
                'default' => 'draft',
            ],
            'approved_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('brand_id', 'brand_profiles', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('campaigns');
    }

    public function down()
    {
        $this->forge->dropTable('campaigns');
    }
}
