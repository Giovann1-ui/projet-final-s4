<?php

namespace App\Models;

use CodeIgniter\Model;

class EpargneModel extends Model
{
    protected $table         = 'epargne';
    protected $primaryKey    = 'id';
    protected $client_id     = 'client_id';
    protected $allowedFields = ['pourcentage'];

    public function getPourcentage(): float
    {
        $promotion = $this->find(1);

        return $promotion !== null ? (float) $promotion['pourcentage'] : 0.0;
    }

    public function updatePourcentage(float $pourcentage): bool
    {
        if ($this->find(1) === null) {
            return $this->insert(['id' => 1, 'pourcentage' => $pourcentage]) !== false;
        }

        return $this->update(1, ['pourcentage' => $pourcentage]);
    }
}
