<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model
{
    protected $table         = 'promotion';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['promotion_pct'];
    protected $useTimestamps = false;

    public function getPromotion()
    {
        return $this->first();    
    }
}
