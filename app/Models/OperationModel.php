<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table         = 'operation';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'type_operation',
        'client_source',
        'client_dest',
        'montant_brut',
        'frais',
        'frais_retrait',
        'commission',
        'montant_entrant',
        'montant_sortant',
        'date',
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

    public function getSoldeTotal(int $clientId): float
    {
        return $this->getSolde($clientId)['solde'];
    }

    /**
     * Solde du client ventilé par type d'opération (DEPOT, RETRAIT, TRANSFERT, ...),
     * utilisé par la vue opérateur "détail client".
     */
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


    public function getGainsByTypeOperationAndPrefixe(int $typeOperationId, array|string $prefixeOperateur): float
    {
        $prefixes = $this->cleanPrefixes($prefixeOperateur);

        if (empty($prefixes)) {
            return 0.0;
        }

        // RETRAIT (2) -> jointure sur client_source, sinon sur client_dest
        $joinColumn = ($typeOperationId === 2) ? 'operation.client_source' : 'operation.client_dest';

        $builder = $this->selectSum('operation.frais', 'total_frais')
            ->where('operation.type_operation', $typeOperationId)
            ->join('client', "client.id = {$joinColumn}");

        $builder->groupStart();
        foreach ($prefixes as $prefixe) {
            $builder->orLike('client.num_tel', $prefixe, 'after');
        }
        $builder->groupEnd();

        $result = $builder->get()->getRowArray();

        return (float) ($result['total_frais'] ?? 0.0);
    }

    public function getGainsByTypeOperationAndPrefixeSQL(int $typeOperationId, array|string $prefixeOperateur): float
    {
        $prefixes = $this->cleanPrefixes($prefixeOperateur);
        if (empty($prefixes)) return 0.0;

        $joinColumn = ($typeOperationId === 2) ? 'operation.client_source' : 'operation.client_dest';

        // Construction dynamique de la clause LIKE
        $likeConditions = [];
        $params = [$typeOperationId];

        foreach ($prefixes as $prefixe) {
            $likeConditions[] = "client.num_tel LIKE ?";
            $params[] = $prefixe . '%'; // 'after' dans CI4 correspond à ajouter '%' à la fin
        }

        $sql = "SELECT SUM(operation.frais) AS total_frais
            FROM operation
            JOIN client ON client.id = {$joinColumn}
            WHERE operation.type_operation = ?
              AND (" . implode(' OR ', $likeConditions) . ")";

        $query = $this->db->query($sql, $params);
        $row = $query->getRowArray();

        return (float) ($row['total_frais'] ?? 0.0);
    }

    public function getOperationByTypeOperationAndPrefixe(int $typeOperationId, array|string $prefixeOperateur): array
    {
        $prefixes = $this->cleanPrefixes($prefixeOperateur);

        if (empty($prefixes)) {
            return [];
        }

        $joinColumn = ($typeOperationId === 2) ? 'operation.client_source' : 'operation.client_dest';

        // Ajout d'un select pour récupérer aussi les numéros des clients sources et destinations
        $builder = $this->select('operation.*, cs.num_tel AS num_tel_source, cd.num_tel AS num_tel_dest')
            ->where('operation.type_operation', $typeOperationId)
            ->join('client cs', 'cs.id = operation.client_source', 'left')
            ->join('client cd', 'cd.id = operation.client_dest', 'left')
            // La jointure de filtrage du préfixe se base sur la règle métier (source ou dest)
            ->join('client', "client.id = {$joinColumn}");

        $builder->groupStart();
        foreach ($prefixes as $prefixe) {
            $builder->orLike('client.num_tel', $prefixe, 'after');
        }
        $builder->groupEnd();

        return $builder->orderBy('operation.date', 'DESC')->findAll();
    }

    public function getOperationByTypeOperationAndPrefixeSQL(int $typeOperationId, array|string $prefixeOperateur): array
    {
        $prefixes = $this->cleanPrefixes($prefixeOperateur);
        if (empty($prefixes)) return [];

        $joinColumn = ($typeOperationId === 2) ? 'operation.client_source' : 'operation.client_dest';

        // Construction dynamique de la clause LIKE
        $likeConditions = [];
        $params = [$typeOperationId];

        foreach ($prefixes as $prefixe) {
            $likeConditions[] = "client.num_tel LIKE ?";
            $params[] = $prefixe . '%'; // 'after' dans CI4 correspond à ajouter '%' à la fin
        }

        $sql = "SELECT operation.*, cs.num_tel AS num_tel_source, cd.num_tel AS num_tel_dest
            FROM operation
            LEFT JOIN client cs ON cs.id = operation.client_source
            LEFT JOIN client cd ON cd.id = operation.client_dest
            JOIN client ON client.id = {$joinColumn}
            WHERE operation.type_operation = ?
              AND (" . implode(' OR ', $likeConditions) . ")
            ORDER BY operation.date DESC";

        $query = $this->db->query($sql, $params);
        return $query->getResultArray();
    }

    public function getOperationsByPrefixe(array|string $prefixeOperateur): array
    {
        $prefixes = $this->cleanPrefixes($prefixeOperateur);

        if (empty($prefixes)) {
            return [];
        }

        $builder = $this->select(
            'operation.*, type_operation.libelle AS type_libelle, ' .
                'cs.num_tel AS num_tel_source, cd.num_tel AS num_tel_dest'
        )
            ->join('type_operation', 'type_operation.id = operation.type_operation')
            ->join('client cs', 'cs.id = operation.client_source', 'left')
            ->join('client cd', 'cd.id = operation.client_dest', 'left');

        $builder->groupStart();
        foreach ($prefixes as $prefixe) {
            $builder->orLike('cs.num_tel', $prefixe, 'after');
            $builder->orLike('cd.num_tel', $prefixe, 'after');
        }
        $builder->groupEnd();

        return $builder->orderBy('operation.date', 'DESC')->findAll();
    }

    private function cleanPrefixes(array|string $prefixeOperateur): array
    {
        $array = is_array($prefixeOperateur) ? $prefixeOperateur : [$prefixeOperateur];

        return array_values(array_filter(array_map(
            static fn($code) => substr(trim((string) $code), 0, 3),
            $array
        )));
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
        // return $this->select('operation.*, type_operation.libelle AS type_libelle')
        //     ->join('type_operation', 'type_operation.id = operation.type_operation', 'left')
        //     ->groupStart()
        //     ->where('operation.client_source', $clientId)
        //     ->orWhere('operation.client_dest', $clientId)
        //     ->groupEnd()
        //     ->orderBy('operation.date', 'DESC')
        //     ->findAll();
        $sql = "SELECT operation.*, cs.num_tel AS num_tel_source, cd.num_tel AS num_tel_dest, type_operation.libelle AS type_libelle
                FROM operation
                LEFT JOIN type_operation ON type_operation.id = operation.type_operation
                LEFT JOIN client cs ON cs.id = operation.client_source
                LEFT JOIN client cd ON cd.id = operation.client_dest
                WHERE operation.client_source = ? OR operation.client_dest = ?
                ORDER BY operation.date DESC";
        $query = $this->db->query($sql, [$clientId, $clientId]);
        return $query->getResultArray();
    }
}
