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

    public function getSolde(int $clientId): array
    {
        $entrant = $this->selectSum('montant_entrant')
            ->where('client_dest', $clientId)
            ->get()->getRowArray()['montant_entrant'] ?? 0;

        $sortant = $this->selectSum('montant_sortant')
            ->where('client_source', $clientId)
            ->get()->getRowArray()['montant_sortant'] ?? 0;

        $entrant = (float) $entrant;
        $sortant = (float) $sortant;

        return [
            'entrant' => $entrant,
            'sortant' => $sortant,
            'solde'   => $entrant - $sortant,
        ];
    }

    public function getHistorique(
        int $clientId,
        ?int $typeOperationId = null,
        ?string $dateDebut = null,
        ?string $dateFin = null
    ): array {
        $builder = $this->select(
                'operation.*, type_operation.libelle AS type_libelle, ' .
                'cs.num_tel AS num_tel_source, cd.num_tel AS num_tel_dest'
            )
            ->join('type_operation', 'type_operation.id = operation.type_operation')
            ->join('client cs', 'cs.id = operation.client_source', 'left')
            ->join('client cd', 'cd.id = operation.client_dest', 'left')
            ->groupStart()
                ->where('operation.client_source', $clientId)
                ->orWhere('operation.client_dest', $clientId)
            ->groupEnd();

        if ($typeOperationId !== null) {
            $builder->where('operation.type_operation', $typeOperationId);
        }
        if ($dateDebut !== null && $dateDebut !== '') {
            $builder->where('operation.date >=', $dateDebut . ' 00:00:00');
        }
        if ($dateFin !== null && $dateFin !== '') {
            $builder->where('operation.date <=', $dateFin . ' 23:59:59');
        }

        $operations = $builder->orderBy('operation.date', 'DESC')->findAll();

        $entrant = $sortant = $frais = 0.0;
        foreach ($operations as $op) {
            if ((int) $op['client_dest'] === $clientId) {
                $entrant += (float) $op['montant_entrant'];
            }
            if ((int) $op['client_source'] === $clientId) {
                $sortant += (float) $op['montant_sortant'];
                $frais   += (float) $op['frais'];
            }
        }

        return [
            'operations' => $operations,
            'totaux'     => [
                'entrant' => $entrant,
                'sortant' => $sortant,
                'frais'   => $frais,
                'nombre'  => count($operations),
            ],
        ];
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
            $client['solde'] = $this->getSolde($client['id']);
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
