<?php

namespace App\Models;

use CodeIgniter\Model;

class EpargneModel extends Model
{
    protected $table         = 'epargne';
    protected $primaryKey    = 'id';

    protected $allowedFields = ['client_id', 'pourcentage', 'solde'];

    public function getByClient(int $clientId): ?array
    {
        return $this->where('client_id', $clientId)->first();
    }

    public function getPourcentage(int $clientId): float
    {
        $epargne = $this->getByClient($clientId);

        return $epargne !== null ? (float) $epargne['pourcentage'] : 0.0;
    }

    public function getSolde(int $clientId): float
    {
        $epargne = $this->getByClient($clientId);

        return $epargne !== null ? (float) $epargne['solde'] : 0.0;
    }

    public function updatePourcentage(int $clientId, float $pourcentage): bool
    {
        $epargne = $this->getByClient($clientId);

        if ($epargne === null) {
            return $this->insert([
                'client_id'   => $clientId,
                'pourcentage' => $pourcentage,
                'solde'       => 0.00,
            ]) !== false;
        }

        return $this->update($epargne['id'], ['pourcentage' => $pourcentage]);
    }

    public function ajouterMontant(int $clientId, float $montant): bool
    {
        $epargne = $this->getByClient($clientId);

        if ($epargne === null) {
            return $this->insert([
                'client_id'   => $clientId,
                'pourcentage' => 0.00,
                'solde'       => $montant,
            ]) !== false;
        }

        return $this->update($epargne['id'], ['solde' => (float) $epargne['solde'] + $montant]);
    }
}
