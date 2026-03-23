<?php

namespace App\Models;

use CodeIgniter\Model;

class InfluencerProfileModel extends Model
{
    protected $table            = 'influencer_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'display_name',
        'bio',
        'niche',
        'country',
        'instagram_handle',
        'tiktok_handle',
        'youtube_handle',
        'followers_count',
        'engagement_rate',
        'managed_by',
    ];
}
