<?php

namespace App\Controllers;

use App\Libraries\FraisService;
use App\Models\OperationModel;
use App\Models\EpargneModel;
use App\Models\TypeOperationModel;
use CodeIgniter\Controller;
use RuntimeException;

class Client extends Controller
{
    
    protected function viewData(array $data = []): array
    {
        return array_merge($data);
    }

    public function index()
    {
        $clientId = session('client')['id'];
        $solde    = (new OperationModel())->getSolde($clientId);

        return view('client/dashboard', ['solde' => $solde]);
    }

    public function depot()
    {
        return view('client/depot');
    }

    public function depotStore()
    {
        if (!$this->validate(['montant' => 'required|numeric|greater_than[0]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $montant  = (float) $this->request->getPost('montant');
        $clientId = session('client')['id'];

        try {
            (new FraisService())->deposer($clientId, $montant);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/depot')->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/client')->with(
            'success',
            'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué.'
        );
    }

    public function retrait()
    {
        return view('client/retrait');
    }

    public function retraitStore()
    {
        if (!$this->validate(['montant' => 'required|numeric|greater_than[0]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $montant  = (float) $this->request->getPost('montant');
        $clientId = session('client')['id'];

        try {
            (new FraisService())->retirer($clientId, $montant);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/retrait')->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/client')->with(
            'success',
            'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué.'
        );
    }

    public function transfert()
    {
        return view('client/transfert');
    }

    public function transfertStore()
    {
        if (!$this->validate([
            'num_tel_dest' => 'required|max_length[15]',
            'montant'      => 'required|numeric|greater_than[0]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $numTelDest           = $this->request->getPost('num_tel_dest');
        $montant              = (float) $this->request->getPost('montant');
        $inclureFraisRetrait  = (bool) $this->request->getPost('inclure_frais_retrait');
        $clientId             = session('client')['id'];

        try {
            (new FraisService())->transferer($clientId, $numTelDest, $montant, $inclureFraisRetrait);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/transfert')->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/client')->with(
            'success',
            'Transfert de ' . number_format($montant, 0, ',', ' ') . " Ar vers {$numTelDest} effectue."
        );
    }

    public function envoiMultiple()
    {
        return view('client/envoi_multiple');
    }

    public function envoiMultipleStore()
    {
        if (!$this->validate([
            'num_tels_dest' => 'required',
            'montant'       => 'required|numeric|greater_than[0]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $numTels             = (array) $this->request->getPost('num_tels_dest');
        $montant             = (float) $this->request->getPost('montant');
        $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');
        $clientId            = session('client')['id'];

        try {
            (new FraisService())->transfererMultiple($clientId, $numTels, $montant, $inclureFraisRetrait);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/envoi-multiple')->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/client')->with(
            'success',
            'Envoi multiple de ' . number_format($montant, 0, ',', ' ') . ' Ar effectue.'
        );
    }

    public function epargne()
    {
        $epargneModel = new EpargneModel();

        return view('client/epargne', $this->viewData([
            'pourcentage' => $epargneModel->getPourcentage(),
        ]));
    }

    public function epargneUpdate()
    {
        // if ($redirect = $this->requireRole('operateur')) return $redirect;
        if (!$this->validate([
            'pourcentage' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $epargneModel = new epargneModel();
        $epargneModel->updatePourcentage((float) $this->request->getPost('pourcentage'));

        return redirect()->to('/client/epargne')->with('success', 'Pourcentage d epargne mis a jour.');
    }

    public function historique()
    {
        $clientId  = session('client')['id'];
        $typeLabel = $this->request->getGet('type');
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $typeId = null;
        if (!empty($typeLabel)) {
            try {
                $typeId = (new TypeOperationModel())->getIdByLibelle($typeLabel);
            } catch (RuntimeException $e) {
                $typeId = null;
            }
        }

        $resultat = (new OperationModel())->getHistorique($clientId, $typeId, $dateDebut, $dateFin);

        return view('client/historique', [
            'operations' => $resultat['operations'],
            'totaux'     => $resultat['totaux'],
            'filtres'    => [
                'type'       => $typeLabel,
                'date_debut' => $dateDebut,
                'date_fin'   => $dateFin,
            ],
        ]);
    }
}
