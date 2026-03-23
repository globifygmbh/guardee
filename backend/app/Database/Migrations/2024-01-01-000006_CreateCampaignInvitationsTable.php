<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCampaignInvitationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'campaign_id' => ['type' => 'INT', 'unsigned' => true],
            'influencer_id' => ['type' => 'INT', 'unsigned' => true],
            'invited_by' => ['type' => 'INT', 'unsigned' => true],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'interested', 'declined', 'withdrawn'],
                'default' => 'pending',
            ],
            'responded_at' => ['type' => 'DATETIME', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('influencer_id', 'influencer_profiles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invited_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addUniqueKey(['campaign_id', 'influencer_id']);
        $this->forge->createTable('campaign_invitations');
    }

    public function down()
    {
        $this->forge->dropTable('campaign_invitations');
    }
}
