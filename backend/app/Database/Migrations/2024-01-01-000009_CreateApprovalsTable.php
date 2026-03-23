<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApprovalsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'approvable_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'approvable_id' => ['type' => 'INT', 'unsigned' => true],
            'approved_by' => ['type' => 'INT', 'unsigned' => true],
            'action' => [
                'type' => 'ENUM',
                'constraint' => ['approved', 'rejected'],
            ],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('approvals');
    }

    public function down()
    {
        $this->forge->dropTable('approvals');
    }
}
