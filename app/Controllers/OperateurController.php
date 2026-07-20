<?php

namespace App\Controllers;

use App\Models\PrefixeOperateurModel;
use App\Models\OperationModel;


class OperateurController extends BaseController {
    public function index() {
        // dou vien redirect ? -> requireRole('operateur') return $redirect;
        // if ($redirect = $this->requireRole('operateur')) return $redirect;

        return view('operateur/dashboard', $this->viewData());
    }

    public function creerOperateur() {
        // if ($redirect = $this->requireRole('operateur')) return $redirect;
        $prefixeModel = new PrefixeOperateurModel();
        $liste = $prefixeModel->getAllPrefixeOperateurs();
        return view('operateur/creerOperateurPrefixe', $this->viewData(['liste' => $liste]));
    }

    public function storeOperateur() {
        $prefixe = $this->request->getPost('prefixe');
        // if ($redirect = $this->requireRole('operateur')) return $redirect;

        $prefixeModel = new PrefixeOperateurModel();
        $prefixeModel->createPrefixeOperateur(['code' => $prefixe]);

        return redirect()->to('/operateur');
    }

    public function situationGains() {
        // if ($redirect = $this->requireRole('operateur')) return $redirect;
        $operationModel = new OperationModel();
        $totalGainsRetrait = $operationModel->getGainsByTypeOperation(2); // Retrait
        $totalGainsTransfert = $operationModel->getGainsByTypeOperation(3); // Transfert
        $listeOperationsRetrait = $operationModel->getOperationByTypeOperation(2);
        $listeOperationsTransfert = $operationModel->getOperationByTypeOperation(3);

        return view('operateur/situationGain', $this->viewData([
            'totalGainsRetrait' => $totalGainsRetrait,
            'totalGainsTransfert' => $totalGainsTransfert,
            'listeOperationsRetrait' => $listeOperationsRetrait,
            'listeOperationsTransfert' => $listeOperationsTransfert,
        ]));
    }
}
