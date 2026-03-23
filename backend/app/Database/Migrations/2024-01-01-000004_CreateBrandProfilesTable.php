<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBrandProfilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'company_name' => ['type' => 'VARCHAR', 'constraint' => 200],
            'industry' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'website' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'logo' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'managed_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('brand_profiles');
    }

    public function down()
    {
        $this->forge->dropTable('brand_profiles');
    }
}
