<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // Seed roles
        $roles = [
            ['id' => 1, 'name' => 'admin', 'label' => 'Platform Admin', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'name' => 'brand', 'label' => 'Brand / Unternehmen', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 3, 'name' => 'influencer', 'label' => 'Influencer / Creator', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 4, 'name' => 'influencer_manager', 'label' => 'Influencer Manager', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 5, 'name' => 'agency', 'label' => 'Agentur', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('roles')->insertBatch($roles);

        // Seed admin user (password: admin123)
        $this->db->table('users')->insert([
            'role_id' => 1,
            'email' => 'admin@guardee.io',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'first_name' => 'Platform',
            'last_name' => 'Admin',
            'status' => 'active',
            'email_verified_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Seed demo brand
        $this->db->table('users')->insert([
            'role_id' => 2,
            'email' => 'brand@demo.com',
            'password' => password_hash('demo123', PASSWORD_BCRYPT),
            'first_name' => 'Demo',
            'last_name' => 'Brand',
            'status' => 'active',
            'email_verified_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('brand_profiles')->insert([
            'user_id' => 2,
            'company_name' => 'Demo Brand GmbH',
            'industry' => 'Fashion',
            'website' => 'https://demo-brand.com',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Seed demo influencer
        $this->db->table('users')->insert([
            'role_id' => 3,
            'email' => 'influencer@demo.com',
            'password' => password_hash('demo123', PASSWORD_BCRYPT),
            'first_name' => 'Demo',
            'last_name' => 'Creator',
            'status' => 'active',
            'email_verified_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('influencer_profiles')->insert([
            'user_id' => 3,
            'display_name' => 'DemoCreator',
            'bio' => 'Lifestyle & Fashion Creator',
            'niche' => 'Fashion',
            'country' => 'DE',
            'instagram_handle' => '@democreator',
            'followers_count' => 150000,
            'engagement_rate' => 3.5,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
