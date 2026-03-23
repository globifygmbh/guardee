<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandProfileModel extends Model
{
    protected $table            = 'brand_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'company_name',
        'industry',
        'website',
        'logo',
        'description',
        'managed_by',
    ];
}
