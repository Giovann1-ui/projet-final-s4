<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table         = 'operation';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'type_operation', 'client_source', 'client_dest',
        'montant_brut', 'frais', 'montant_entrant', 'montant_sortant', 'date',
    ];
    protected $useTimestamps = false;

    public function getSoldeParTypeOperation(int $clientId): array
    {
        $builder = $this->db->table('type_operation to');
        $builder->select("
                to.id,
                to.libelle,
                COALESCE(SUM(CASE WHEN o.client_dest = " . (int) $clientId . " THEN o.montant_entrant ELSE 0 END), 0) AS entrant,
                COALESCE(SUM(CASE WHEN o.client_source = " . (int) $clientId . " THEN o.montant_sortant ELSE 0 END), 0) AS sortant
            ")
            ->join('operation o', "o.type_operation = to.id AND (o.client_dest = {$clientId} OR o.client_source = {$clientId})", 'left')
            ->groupBy('to.id, to.libelle')
            ->orderBy('to.id', 'ASC');

        $rows = $builder->get()->getResultArray();

        foreach ($rows as &$row) {
            $row['entrant'] = (float) $row['entrant'];
            $row['sortant'] = (float) $row['sortant'];
            $row['solde']   = $row['entrant'] - $row['sortant'];
        }

        return $rows;
    }

    public function getSoldeTotal(int $clientId): float
    {
        $entrant = $this->selectSum('montant_entrant')
            ->where('client_dest', $clientId)
            ->get()->getRowArray()['montant_entrant'] ?? 0;

        $sortant = $this->selectSum('montant_sortant')
            ->where('client_source', $clientId)
            ->get()->getRowArray()['montant_sortant'] ?? 0;

        return (float) $entrant - (float) $sortant;
    }

    public function getGainsByTypeOperation(int $typeOperationId): float
    {
        $gain = $this->selectSum('frais')
            ->where('type_operation', $typeOperationId)
            ->get()->getRowArray()['frais'] ?? 0;

        return (float) $gain;
    }

    public function getOperationByTypeOperation(int $typeOperationId): array
    {
        return $this->where('type_operation', $typeOperationId)
            ->orderBy('date', 'DESC')
            ->findAll();
    }

    public function getAllClientsWithSolde(): array
    {
        $clientModel = new ClientModel();
        $clients = $clientModel->findAll();

        foreach ($clients as &$client) {
            $client['solde'] = $this->getSoldeTotal($client['id']);
        }

        return $clients;
    }

    public function getTransactionsByClient(int $clientId): array
    {
        return $this->select('operation.*, type_operation.libelle AS type_libelle')
            ->join('type_operation', 'type_operation.id = operation.type_operation', 'left')
            ->groupStart()
                ->where('operation.client_source', $clientId)
                ->orWhere('operation.client_dest', $clientId)
            ->groupEnd()
            ->orderBy('operation.date', 'DESC')
            ->findAll();
    }
}
