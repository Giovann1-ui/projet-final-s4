<?php

namespace App\Libraries;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\PrefixeOperateurModel;
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
    protected PrefixeOperateurModel $prefixeOperateurModel;
    protected $db;

    public function __construct(
        ?ClientModel $clientModel = null,
        ?OperationModel $operationModel = null,
        ?TypeOperationModel $typeOperationModel = null,
        ?BaremeFraisModel $baremeFraisModel = null,
        ?PrefixeOperateurModel $prefixeOperateurModel = null
    ) {
        $this->clientModel          = $clientModel ?? new ClientModel();
        $this->operationModel       = $operationModel ?? new OperationModel();
        $this->typeOperationModel   = $typeOperationModel ?? new TypeOperationModel();
        $this->baremeFraisModel     = $baremeFraisModel ?? new BaremeFraisModel();
        $this->prefixeOperateurModel = $prefixeOperateurModel ?? new PrefixeOperateurModel();
        $this->db                   = Database::connect();
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
                'Aucun bareme de frais ne correspond au montant ' . number_format($montant, 0, ',', ' ')
                . " Ar pour l'operation {$typeLibelle}. Veuillez reessayer avec un autre montant."
            );
        }

        return (float) $tranche['frais'];
    }

    public function verifierSoldeSuffisant(int $clientId, float $montantRequis): bool
    {
        return $this->operationModel->getSolde($clientId)['solde'] >= $montantRequis;
    }

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
            'frais_retrait'   => 0,
            'montant_entrant' => $montant,
            'montant_sortant' => 0,
            'date'            => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();
        $this->assertTransactionOk();

        return $this->operationModel->find($id);
    }

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
            'frais_retrait'   => 0,
            'montant_entrant' => 0,
            'montant_sortant' => $montantSortant,
            'date'            => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();
        $this->assertTransactionOk();

        return $this->operationModel->find($id);
    }

    public function transferer(
        int $clientIdSource,
        string $numTelDestinataire,
        float $montant,
        bool $inclureFraisRetrait = false
    ): array {
        $this->assertMontantPositif($montant);

        $source = $this->clientModel->find($clientIdSource);
        $dest   = $this->clientModel->findByNumTel($numTelDestinataire);

        if ($dest === null) {
            throw new RuntimeException("Destinataire introuvable pour le numero {$numTelDestinataire}.");
        }
        if ((int) $dest['id'] === $clientIdSource) {
            throw new RuntimeException('Vous ne pouvez pas transferer vers votre propre numero.');
        }
        if ($inclureFraisRetrait && !$this->memeOperateur($source['num_tel'], $numTelDestinataire)) {
            throw new RuntimeException(
                "L'inclusion des frais de retrait n'est disponible qu'entre numeros du meme operateur."
            );
        }

        $fraisTransfert = $this->calculerFrais(self::TRANSFERT, $montant);
        $fraisRetrait   = $inclureFraisRetrait ? $this->calculerFrais(self::RETRAIT, $montant) : 0.0;
        $montantEntrant = $montant + $fraisRetrait;
        $montantSortant = $montant + $fraisTransfert + $fraisRetrait;

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
            'frais'           => $fraisTransfert,
            'frais_retrait'   => $fraisRetrait,
            'montant_entrant' => $montantEntrant,
            'montant_sortant' => $montantSortant,
            'date'            => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();
        $this->assertTransactionOk();

        return $this->operationModel->find($id);
    }

    public function transfererMultiple(
        int $clientIdSource,
        array $numTelsDestinataires,
        float $montantTotal,
        bool $inclureFraisRetrait = false
    ): array {
        $this->assertMontantPositif($montantTotal);

        $numTelsDestinataires = array_values(array_unique(array_filter(
            array_map('trim', $numTelsDestinataires),
            static fn ($n) => $n !== ''
        )));

        if (count($numTelsDestinataires) < 2) {
            throw new RuntimeException('Veuillez indiquer au moins deux numeros de destinataires pour un envoi multiple.');
        }

        $source                 = $this->clientModel->find($clientIdSource);
        $nombreDestinataires    = count($numTelsDestinataires);
        $montantParDestinataire = round($montantTotal / $nombreDestinataires, 2);

        $destinataires = [];
        foreach ($numTelsDestinataires as $numTel) {
            $dest = $this->clientModel->findByNumTel($numTel);
            if ($dest === null) {
                throw new RuntimeException("Destinataire introuvable pour le numero {$numTel}.");
            }
            if ((int) $dest['id'] === $clientIdSource) {
                throw new RuntimeException('Vous ne pouvez pas transferer vers votre propre numero.');
            }
            if (!$this->memeOperateur($source['num_tel'], $numTel)) {
                throw new RuntimeException(
                    "L'envoi multiple n'est autorise qu'entre numeros du meme operateur ({$numTel} a un prefixe different)."
                );
            }
            $destinataires[] = $dest;
        }

        $fraisTransfert         = $this->calculerFrais(self::TRANSFERT, $montantParDestinataire);
        $fraisRetrait           = $inclureFraisRetrait ? $this->calculerFrais(self::RETRAIT, $montantParDestinataire) : 0.0;
        $montantEntrant         = $montantParDestinataire + $fraisRetrait;
        $montantSortantUnitaire = $montantParDestinataire + $fraisTransfert + $fraisRetrait;
        $montantSortantTotal    = $montantSortantUnitaire * $nombreDestinataires;
        $typeId                 = $this->typeOperationModel->getIdByLibelle(self::TRANSFERT);

        $this->db->transStart();

        if (!$this->verifierSoldeSuffisant($clientIdSource, $montantSortantTotal)) {
            $this->db->transRollback();
            throw new RuntimeException(
                'Solde insuffisant pour cet envoi multiple (total requis = '
                . number_format($montantSortantTotal, 0, ',', ' ') . ' Ar).'
            );
        }

        $operations = [];
        foreach ($destinataires as $dest) {
            $id = $this->operationModel->insert([
                'type_operation'  => $typeId,
                'client_source'   => $clientIdSource,
                'client_dest'     => (int) $dest['id'],
                'montant_brut'    => $montantParDestinataire,
                'frais'           => $fraisTransfert,
                'frais_retrait'   => $fraisRetrait,
                'montant_entrant' => $montantEntrant,
                'montant_sortant' => $montantSortantUnitaire,
                'date'            => date('Y-m-d H:i:s'),
            ]);
            $operations[] = $this->operationModel->find($id);
        }

        $this->db->transComplete();
        $this->assertTransactionOk();

        return $operations;
    }

    /**
     * Deux numeros appartiennent au meme operateur si leurs prefixes (les 3 premiers
     * chiffres) sont rattaches au meme operateur_id dans `prefixe_operateur` — un meme
     * operateur peut avoir plusieurs prefixes, donc on ne compare jamais les prefixes
     * directement entre eux.
     */
    private function memeOperateur(string $numTel1, string $numTel2): bool
    {
        $operateurId1 = $this->prefixeOperateurModel->getOperateurIdByPrefixe(substr($numTel1, 0, 3));
        $operateurId2 = $this->prefixeOperateurModel->getOperateurIdByPrefixe(substr($numTel2, 0, 3));

        return $operateurId1 !== null && $operateurId1 === $operateurId2;
    }

    private function assertMontantPositif(float $montant): void
    {
        if ($montant <= 0) {
            throw new RuntimeException('Le montant doit etre superieur à 0.');
        }
    }

    private function assertTransactionOk(): void
    {
        if ($this->db->transStatus() === false) {
            throw new RuntimeException("Erreur lors de l'enregistrement de l'operation, veuillez reessayer.");
        }
    }
}
