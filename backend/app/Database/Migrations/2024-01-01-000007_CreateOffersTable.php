<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOffersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'campaign_id' => ['type' => 'INT', 'unsigned' => true],
            'invitation_id' => ['type' => 'INT', 'unsigned' => true],
            'brand_id' => ['type' => 'INT', 'unsigned' => true],
            'influencer_id' => ['type' => 'INT', 'unsigned' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'currency' => ['type' => 'VARCHAR', 'constraint' => 3, 'default' => 'EUR'],
            'message' => ['type' => 'TEXT', 'null' => true],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted', 'declined', 'revised'],
                'default' => 'pending',
            ],
            'terms_accepted' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'terms_accepted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invitation_id', 'campaign_invitations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('brand_id', 'brand_profiles', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('influencer_id', 'influencer_profiles', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('offers');
    }

    public function down()
    {
        $this->forge->dropTable('offers');
    }
}
