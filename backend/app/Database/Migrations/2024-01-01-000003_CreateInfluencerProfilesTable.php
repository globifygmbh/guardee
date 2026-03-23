<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInfluencerProfilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'display_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'bio' => ['type' => 'TEXT', 'null' => true],
            'niche' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'country' => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'instagram_handle' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tiktok_handle' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'youtube_handle' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'followers_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'engagement_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'managed_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('influencer_profiles');
    }

    public function down()
    {
        $this->forge->dropTable('influencer_profiles');
    }
}
