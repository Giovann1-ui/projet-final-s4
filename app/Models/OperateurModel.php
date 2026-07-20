<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table = 'operateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['libelle', 'isUs', 'commission'];

    public function getAllOperateurs()
    {
        return $this->findAll();
    }
}