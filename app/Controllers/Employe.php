<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class Employe extends BaseController
{
    public function index()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $employeId  = $this->employe['id'];

        $soldeModel = new SoldeModel();
        $congeModel = new CongeModel();

        $soldes  = $soldeModel->getSoldesEmploye($employeId);
        $total   = 0;
        foreach ($soldes as $s) {
            if ($s['deductible']) {
                $total += (int) $s['reste'];
            }
        }

        $demandes = $congeModel->getDemandesEmploye($employeId);
        $statsParType = $congeModel->getStatsParTypeEmploye($employeId);

        $en_attente = 0;
        $approuvees = 0;
        foreach ($demandes as $d) {
            if ($d['statut'] === 'en_attente') $en_attente++;
            if ($d['statut'] === 'approuvee') $approuvees++;
        }

        return view('employe/dashboard', $this->viewData([
            'soldes'     => $soldes,
            'solde'      => $total,
            'demandes'   => $demandes,
            'en_attente' => $en_attente,
            'approuvees' => $approuvees,
            'stats_par_type' => $statsParType,
        ]));
    }

    public function demande()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $typeCongeModel = new TypeCongeModel();
        $soldeModel     = new SoldeModel();

        $types  = $typeCongeModel->findAll();
        $soldes = $soldeModel->getSoldesEmploye($this->employe['id']);

        $soldeParType = [];
        foreach ($soldes as $s) {
            $soldeParType[$s['type_conge_id']] = $s['reste'];
        }

        return view('employe/demande', $this->viewData([
            'types'         => $types,
            'soldeParType'  => $soldeParType,
            'soldes'        => $soldes,
            'validation'    => null,
        ]));
    }

    public function store()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $rules = [
            'type_conge_id' => 'required|integer|is_not_unique[types_conge.id]',
            'date_debut'    => 'required|valid_date',
            'date_fin'      => 'required|valid_date',
            'motif'         => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return $this->redemander();
        }

        $employeId    = $this->employe['id'];
        $typeCongeId  = (int) $this->request->getPost('type_conge_id');
        $dateDebut    = $this->request->getPost('date_debut');
        $dateFin      = $this->request->getPost('date_fin');
        $motif        = $this->request->getPost('motif');

        if ($dateDebut > $dateFin) {
            return redirect()->back()
                ->with('error', 'La date de fin doit être postérieure à la date de début.')
                ->withInput();
        }

        $nbJours = compter_jours_ouvrables($dateDebut, $dateFin);
        if ($nbJours <= 0) {
            return redirect()->back()
                ->with('error', 'La période ne contient aucun jour ouvrable.')
                ->withInput();
        }

        $typeCongeModel = new TypeCongeModel();
        $typeConge      = $typeCongeModel->find($typeCongeId);
        $deductible     = $typeConge && (int) $typeConge['deductible'] === 1;

        if ($deductible) {
            $soldeModel = new SoldeModel();
            $annee      = (int) date('Y', strtotime($dateDebut));
            $reste      = $soldeModel->getReste($employeId, $typeCongeId, $annee);

            if ($nbJours > $reste) {
                return redirect()->back()
                    ->with('error', 'Solde insuffisant. Il vous reste ' . $reste . ' jour(s) pour ce type de congé.')
                    ->withInput();
            }
        }

        $congeModel = new CongeModel();
        if ($congeModel->verifierChevauchement($employeId, $dateDebut, $dateFin)) {
            return redirect()->back()
                ->with('error', 'Vous avez déjà une demande de congé qui chevauche cette période.')
                ->withInput();
        }

        $congeModel->insert([
            'employe_id'    => $employeId,
            'type_conge_id' => $typeCongeId,
            'date_debut'    => $dateDebut,
            'date_fin'      => $dateFin,
            'nb_jours'      => $nbJours,
            'motif'         => $motif,
            'statut'        => 'en_attente',
        ]);

        return redirect()->to('employe')
            ->with('success', 'Votre demande de congé a été soumise avec succès.');
    }

    public function mesDemandes()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $congeModel = new CongeModel();
        $demandes   = $congeModel->getDemandesEmploye($this->employe['id']);

        return view('employe/mes_demandes', $this->viewData([
            'demandes' => $demandes,
        ]));
    }

    public function annuler(int $id)
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $congeModel = new CongeModel();
        $demande    = $congeModel->find($id);

        if (!$demande || (int) $demande['employe_id'] !== (int) $this->employe['id']) {
            return redirect()->to('employe/mes-demandes')
                ->with('error', 'Demande introuvable.');
        }

        if (!in_array($demande['statut'], ['en_attente', 'approuvee'])) {
            return redirect()->to('employe/mes-demandes')
                ->with('error', 'Cette demande ne peut plus être annulée.');
        }

        $this->recrediterSoldeSiNecessaire($demande);

        $congeModel->update($id, ['statut' => 'annulee']);

        return redirect()->to('employe/mes-demandes')
            ->with('success', 'Votre demande de congé a été annulée.');
    }

    public function solde()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $soldeModel = new SoldeModel();
        $soldes     = $soldeModel->getSoldesEmploye($this->employe['id']);

        return view('employe/solde', $this->viewData([
            'soldes' => $soldes,
        ]));
    }

    public function profil()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $employeModel     = new EmployeModel();
        $departementModel = new DepartementModel();
        $employe          = $employeModel
            ->select('employes.*, departements.nom as departement_nom')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->find($this->employe['id']);

        return view('employe/profil', $this->viewData([
            'employe_info' => $employe,
            'validation'   => null,
        ]));
    }

    public function profilUpdate()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $employeModel = new EmployeModel();
        $employe      = $employeModel->find($this->employe['id']);

        $data = [
            'id'      => $this->employe['id'],
            'nom'     => $this->request->getPost('nom'),
            'prenom'  => $this->request->getPost('prenom'),
            'email'   => $this->request->getPost('email'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $employeModel->setValidationRule('role', 'permit_empty');
        $employeModel->setValidationRule('departement_id', 'permit_empty');
        $employeModel->setValidationRule('date_embauche', 'permit_empty');

        if (!$employeModel->save($data)) {
            $departementModel = new DepartementModel();
            $employeInfo = $employeModel
                ->select('employes.*, departements.nom as departement_nom')
                ->join('departements', 'departements.id = employes.departement_id', 'left')
                ->find($this->employe['id']);

            return view('employe/profil', $this->viewData([
                'employe_info' => $employeInfo,
                'validation'   => $employeModel->errors(),
            ]));
        }

        $this->session->set('employe', $employeModel->find($this->employe['id']));

        return redirect()->to('employe/profil')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    private function redemander()
    {
        $typeCongeModel = new TypeCongeModel();
        $soldeModel     = new SoldeModel();

        $types  = $typeCongeModel->findAll();
        $soldes = $soldeModel->getSoldesEmploye($this->employe['id']);

        $soldeParType = [];
        foreach ($soldes as $s) {
            $soldeParType[$s['type_conge_id']] = $s['reste'];
        }

        return view('employe/demande', $this->viewData([
            'types'        => $types,
            'soldeParType' => $soldeParType,
            'soldes'       => $soldes,
            'validation'   => $this->validator,
        ]));
    }

    public function calendrier()
    {
        if ($redirect = $this->requireRole('employe')) return $redirect;

        $congeModel = new CongeModel();
        $demandes   = $congeModel->getDemandesEmploye($this->employe['id']);

        return view('employe/calendrier', $this->viewData([
            'demandes' => $demandes,
        ]));
    }

}
