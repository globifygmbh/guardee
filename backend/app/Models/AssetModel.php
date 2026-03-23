<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table            = 'assets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'campaign_id',
        'offer_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'asset_type',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];
}
