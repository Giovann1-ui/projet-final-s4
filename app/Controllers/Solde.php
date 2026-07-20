<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;
use CodeIgniter\Controller;

class Solde extends Controller
{
    public function index()
    {
        $numTel = trim((string) $this->request->getGet('num_tel'));

        $data = [
            'num_tel' => $numTel,
            'client'  => null,
            'solde'   => null,
            'erreur'  => null,
        ];

        if ($numTel !== '') {
            $clientModel = new ClientModel();
            $client      = $clientModel->findByNumTel($numTel);

            if (!$client) {
                $data['erreur'] = "Aucun client trouvé pour le numéro {$numTel}.";
            } else {
                $operationModel = new OperationModel();
                $data['client']  = $client;
                $data['solde']   = $operationModel->getSolde((int) $client['id']);
            }
        }

        return view('solde/index', $data);
    }
}
