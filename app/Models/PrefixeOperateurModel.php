<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table = 'prefixe_operateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code'];

    public function getAllPrefixeOperateurs()
    {
        return $this->findAll();
    }

    public function getPrefixeOperateurById($id = null)
    {
        return $this->find($id);
    }

    public function createPrefixeOperateur($data = [])
    {
        return $this->insert($data);
    }

    public function isPrefixeValide(string $prefixe): bool
    {
        return $this->where('code', $prefixe)->first() !== null;
    }
}