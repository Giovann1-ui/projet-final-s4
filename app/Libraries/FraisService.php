<?php

namespace App\Libraries;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\TypeOperationModel;
use Config\Database;
use RuntimeException;

class FraisService
{
    public const DEPOT     = 'DEPOT';
    public const RETRAIT   = 'RETRAIT';
    public const TRANSFERT = 'TRANSFERT';

    protected ClientModel $clientModel;
    protected OperationModel $operationModel;
    protected TypeOperationModel $typeOperationModel;
    protected BaremeFraisModel $baremeFraisModel;
    protected $db;

    public function __construct(
        ?ClientModel $clientModel = null,
        ?OperationModel $operationModel = null,
        ?TypeOperationModel $typeOperationModel = null,
        ?BaremeFraisModel $baremeFraisModel = null
    ) {
        $this->clientModel        = $clientModel ?? new ClientModel();
        $this->operationModel     = $operationModel ?? new OperationModel();
        $this->typeOperationModel = $typeOperationModel ?? new TypeOperationModel();
        $this->baremeFraisModel   = $baremeFraisModel ?? new BaremeFraisModel();
        $this->db                 = Database::connect();
    }

    public function calculerFrais(string $typeLibelle, float $montant): float
    {
        if ($typeLibelle === self::DEPOT) {
            return 0.0;
        }

        $typeId  = $this->typeOperationModel->getIdByLibelle($typeLibelle);
        $tranche = $this->baremeFraisModel->findTranche($typeId, $montant);

        if ($tranche === null) {
            throw new RuntimeException(
                'Aucun barème de frais ne correspond au montant ' . number_format($montant, 0, ',', ' ')
                . " Ar pour l'opération {$typeLibelle}. Veuillez réessayer avec un autre montant."
            );
        }

        return (float) $tranche['frais'];
    }

    public function verifierSoldeSuffisant(int $clientId, float $montantRequis): bool
    {
        return $this->operationModel->getSolde($clientId)['solde'] >= $montantRequis;
    }

    /** @return array la ligne d'opération insérée */
    public function deposer(int $clientId, float $montant): array
    {
        $this->assertMontantPositif($montant);

        $this->db->transStart();
        $id = $this->operationModel->insert([
            'type_operation'  => $this->typeOperationModel->getIdByLibelle(self::DEPOT),
            'client_source'   => null,
            'client_dest'     => $clientId,
            'montant_brut'    => $montant,
            'frais'           => 0,
            'montant_entrant' => $montant,
            'montant_sortant' => 0,
            'date'            => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();
        $this->assertTransactionOk();

        return $this->operationModel->find($id);
    }

    /** @return array la ligne d'opération insérée */
    public function retirer(int $clientId, float $montant): array
    {
        $this->assertMontantPositif($montant);
        $frais          = $this->calculerFrais(self::RETRAIT, $montant);
        $montantSortant = $montant + $frais;

        $this->db->transStart();

        if (!$this->verifierSoldeSuffisant($clientId, $montantSortant)) {
            $this->db->transRollback();
            throw new RuntimeException(
                'Solde insuffisant pour effectuer ce retrait (montant + frais = '
                . number_format($montantSortant, 0, ',', ' ') . ' Ar).'
            );
        }

        $id = $this->operationModel->insert([
            'type_operation'  => $this->typeOperationModel->getIdByLibelle(self::RETRAIT),
            'client_source'   => $clientId,
            'client_dest'     => null,
            'montant_brut'    => $montant,
            'frais'           => $frais,
            'montant_entrant' => 0,
            'montant_sortant' => $montantSortant,
            'date'            => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();
        $this->assertTransactionOk();

        return $this->operationModel->find($id);
    }

    /** @return array la ligne d'opération insérée */
    public function transferer(int $clientIdSource, string $numTelDestinataire, float $montant): array
    {
        $this->assertMontantPositif($montant);

        $dest = $this->clientModel->findByNumTel($numTelDestinataire);
        if ($dest === null) {
            throw new RuntimeException("Destinataire introuvable pour le numéro {$numTelDestinataire}.");
        }
        if ((int) $dest['id'] === $clientIdSource) {
            throw new RuntimeException('Vous ne pouvez pas transférer vers votre propre numéro.');
        }

        $frais          = $this->calculerFrais(self::TRANSFERT, $montant);
        $montantSortant = $montant + $frais;

        $this->db->transStart();

        if (!$this->verifierSoldeSuffisant($clientIdSource, $montantSortant)) {
            $this->db->transRollback();
            throw new RuntimeException(
                'Solde insuffisant pour effectuer ce transfert (montant + frais = '
                . number_format($montantSortant, 0, ',', ' ') . ' Ar).'
            );
        }

        $id = $this->operationModel->insert([
            'type_operation'  => $this->typeOperationModel->getIdByLibelle(self::TRANSFERT),
            'client_source'   => $clientIdSource,
            'client_dest'     => (int) $dest['id'],
            'montant_brut'    => $montant,
            'frais'           => $frais,
            'montant_entrant' => $montant,
            'montant_sortant' => $montantSortant,
            'date'            => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();
        $this->assertTransactionOk();

        return $this->operationModel->find($id);
    }

    private function assertMontantPositif(float $montant): void
    {
        if ($montant <= 0) {
            throw new RuntimeException('Le montant doit être supérieur à 0.');
        }
    }

    private function assertTransactionOk(): void
    {
        if ($this->db->transStatus() === false) {
            throw new RuntimeException("Erreur lors de l'enregistrement de l'opération, veuillez réessayer.");
        }
    }
}
