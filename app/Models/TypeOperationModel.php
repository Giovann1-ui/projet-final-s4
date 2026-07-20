<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class TypeOperationModel extends Model
{
    protected $table         = 'type_operation';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['libelle'];
    protected $useTimestamps = false;

    public function getIdByLibelle(string $libelle): int
    {
        $row = $this->where('libelle', strtoupper($libelle))->first();

        if ($row === null) {
            throw new RuntimeException("Type d'opération inconnu : {$libelle}.");
        }

        return (int) $row['id'];
    }
}
