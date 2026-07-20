<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeOperateurModel;
use CodeIgniter\Controller;

class ClientAuth extends Controller
{
    public function index()
    {
        if (session('client') !== null) {
            return redirect()->to('/client');
        }

        return view('client/login');
    }

    public function login()
    {
        if (!$this->validate([
            'num_tel' => 'required|max_length[15]|regex_match[/^[0-9]{7,15}$/]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $numTel      = $this->request->getPost('num_tel');
        $clientModel = new ClientModel();
        $client      = $clientModel->findByNumTel($numTel);

        if ($client === null) {
            $prefixe       = substr($numTel, 0, 3);
            $prefixeModel  = new PrefixeOperateurModel();

            if (!$prefixeModel->isPrefixeValide($prefixe)) {
                return redirect()->back()->withInput()->with(
                    'error',
                    "Préfixe opérateur invalide ({$prefixe}). Numéro non reconnu."
                );
            }

            $client = $clientModel->getOrCreate($numTel);
        }

        session()->set('client', [
            'id'      => (int) $client['id'],
            'num_tel' => $client['num_tel'],
        ]);

        return redirect()->to('/client')->with('success', 'Connecté avec succès.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/client/login');
    }
}
