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

    public function getallPrefixeOperateursByUs()
    {
        $sql = "SELECT po.code --, o.libelle AS operateur_libelle
                FROM prefixe_operateur po
                JOIN operateur o ON po.operateur_id = o.id
                WHERE o.isUs = 1";
        return $this->db->query($sql)->getResultArray();
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