<?php

namespace App\Models;

use CodeIgniter\Model;

class OfferModel extends Model
{
    protected $table            = 'offers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'campaign_id',
        'invitation_id',
        'brand_id',
        'influencer_id',
        'amount',
        'currency',
        'message',
        'status',
        'terms_accepted',
        'terms_accepted_at',
    ];
}
