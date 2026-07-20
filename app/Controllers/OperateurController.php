<?php

namespace App\Controllers;

use App\Models\PrefixeOperateurModel;

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
}
