<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table = 'prefixe_operateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'operateur_id'];

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

    public function getOperateurIdByPrefixe(string $prefixe): ?int
    {
        $row = $this->where('code', $prefixe)->first();

        return $row !== null ? (int) $row['operateur_id'] : null;
    }
}