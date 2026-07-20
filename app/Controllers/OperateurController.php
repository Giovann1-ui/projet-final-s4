<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeOperateurModel;
use App\Models\OperationModel;


class OperateurController extends BaseController
{
    public function index()
    {
        // dou vien redirect ? -> requireRole('operateur') return $redirect;
        // if ($redirect = $this->requireRole('operateur')) return $redirect;

        return view('operateur/dashboard', $this->viewData());
    }

    public function creerOperateur()
    {
        // if ($redirect = $this->requireRole('operateur')) return $redirect;
        $prefixeModel = new PrefixeOperateurModel();
        $liste = $prefixeModel->getAllPrefixeOperateurs();
        return view('operateur/creerOperateurPrefixe', $this->viewData(['liste' => $liste]));
    }

    public function storeOperateur()
    {
        $prefixe = $this->request->getPost('prefixe');
        // if ($redirect = $this->requireRole('operateur')) return $redirect;

        $prefixeModel = new PrefixeOperateurModel();
        $prefixeModel->createPrefixeOperateur(['code' => $prefixe]);

        return redirect()->to('/operateur');
    }

    public function situationGains()
    {
        // Enlève le commentaire si la gestion des rôles est active :
        // if ($redirect = $this->requireRole('operateur')) return $redirect;

        $operationModel = new OperationModel();
        $prefixeModel = new PrefixeOperateurModel();

        // Récupération de tous les préfixes configurés en BDD (ex: ['034', '038', '033'])
        $prefixeOperateur = $prefixeModel->getAllPrefixeOperateurs();
        $prefixeCodes = array_column($prefixeOperateur, 'code');

        // Calcul des totaux
        $totalGainsRetrait   = $operationModel->getGainsByTypeOperationAndPrefixe(2, $prefixeCodes); // 2 = Retrait
        $totalGainsTransfert = $operationModel->getGainsByTypeOperationAndPrefixe(3, $prefixeCodes); // 3 = Transfert

        // Récupération des listes
        $listeOperationsRetrait   = $operationModel->getOperationByTypeOperationAndPrefixe(2, $prefixeCodes);
        $listeOperationsTransfert = $operationModel->getOperationByTypeOperationAndPrefixe(3, $prefixeCodes);

        return view('operateur/situationGain', $this->viewData([
            'totalGainsRetrait'        => $totalGainsRetrait,
            'totalGainsTransfert'      => $totalGainsTransfert,
            'listeOperationsRetrait'   => $listeOperationsRetrait,
            'listeOperationsTransfert' => $listeOperationsTransfert,
        ]));
    }

    public function situationClients()
    {
        // if ($redirect = $this->requireRole('operateur')) return $redirect;
        $operationModel = new OperationModel();
        $listeClients = $operationModel->getAllClientsWithSolde();

        return view('operateur/situationClient', $this->viewData([
            'listeClients' => $listeClients,
        ]));
    }

    public function situationClientDetail($clientId)
    {
        // if ($redirect = $this->requireRole('operateur')) return $redirect;
        $clientModel = new ClientModel();
        $operationModel = new OperationModel();
        $client = $clientModel->find((int) $clientId);
        $soldeParTypeOperation = $operationModel->getSoldeParTypeOperation($clientId);
        $soldeTotal = $operationModel->getSoldeTotal($clientId);
        $transactions = $operationModel->getTransactionsByClient($clientId);

        if (!$client) {
            return redirect()->to('/operateur/situation-clients');
        }

        return view('operateur/situationClientDetails', $this->viewData([
            'client' => $client,
            'clientId' => $clientId,
            'soldeParTypeOperation' => $soldeParTypeOperation,
            'soldeTotal' => $soldeTotal,
            'transactions' => $transactions,
        ]));
    }
}
