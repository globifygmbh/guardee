<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignModel extends Model
{
    protected $table            = 'campaigns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'brand_id',
        'created_by',
        'title',
        'description',
        'budget',
        'target_audience',
        'countries',
        'start_date',
        'end_date',
        'briefing_file',
        'status',
        'approved_by',
        'approved_at',
    ];
}
