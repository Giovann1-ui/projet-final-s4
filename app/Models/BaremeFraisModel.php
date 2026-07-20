<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table         = 'bareme_frais';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['type_operation', 'montant_min', 'montant_max', 'frais'];
    protected $useTimestamps = false;

    public function findTranche(int $typeOperationId, float $montant): ?array
    {
        return $this->where('type_operation', $typeOperationId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->orderBy('montant_min', 'ASC')
            ->first();
    }
}
