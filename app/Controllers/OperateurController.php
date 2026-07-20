<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperateurModel;
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
        // if ($redirect = $this->requireRole('operateur')) return $redirect;

        $operateurModel = new OperateurModel();
        $operationModel = new OperationModel();
        $prefixeModel = new PrefixeOperateurModel();

        // 1. Récupérer les autres opérateurs (isUs = 0)
        $autresOperateurs = $operateurModel
            ->where('isUs', 0)
            ->orderBy('libelle', 'ASC')
            ->findAll();

        // 2. Récupérer TOUS les préfixes (pour vos propres gains Retraits / Transferts)
        $prefixeOperateur = $prefixeModel->getAllPrefixeOperateurs();
        $prefixeCodes = array_column($prefixeOperateur, 'code');

        // 3. Récupérer les préfixes uniquement des AUTRES opérateurs pour la table du milieu
        $autresPrefixeCodes = [];
        if (!empty($autresOperateurs)) {
            $autresOperateursIds = array_column($autresOperateurs, 'id');
            $prefixeAutres = $prefixeModel->whereIn('operateur_id', $autresOperateursIds)->findAll();
            $autresPrefixeCodes = array_column($prefixeAutres, 'code');
        }

        // Calcul des totaux et listes (Notre réseau "isUs")
        $totalGainsRetrait   = $operationModel->getGainsByTypeOperationAndPrefixe(2, $prefixeCodes);
        $totalGainsTransfert = $operationModel->getGainsByTypeOperationAndPrefixe(3, $prefixeCodes);
        
        $listeOperationsRetrait   = $operationModel->getOperationByTypeOperationAndPrefixe(2, $prefixeCodes);
        $listeOperationsTransfert = $operationModel->getOperationByTypeOperationAndPrefixe(3, $prefixeCodes);

        // 4. Récupération de la liste manquante pour la vue !
        $listeOperationsAutres = [];
        if (!empty($autresPrefixeCodes)) {
            $listeOperationsAutres = $operationModel->getOperationsByPrefixe($autresPrefixeCodes);
        }

        return view('operateur/situationGain', $this->viewData([
            'totalGainsRetrait'        => $totalGainsRetrait,
            'totalGainsTransfert'      => $totalGainsTransfert,
            'listeOperationsRetrait'   => $listeOperationsRetrait,
            'listeOperationsTransfert' => $listeOperationsTransfert,
            'autresOperateurs'         => $autresOperateurs,
            'listeOperationsAutres'    => $listeOperationsAutres, // <-- Variable ajoutée ici
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
