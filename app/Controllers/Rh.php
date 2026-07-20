<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;

class Rh extends BaseController
{
    public function index()
    {
        if ($redirect = $this->requireRole('rh')) return $redirect;

        $congeModel   = new CongeModel();
        $employeModel = new EmployeModel();

        $mois  = date('n');
        $annee = date('Y');

        $absences        = $congeModel->getAbsencesMois($mois, $annee);
        $en_attente      = count($congeModel->getDemandesEnAttente());
        $approuvees_mois = count($absences);
        $nb_employes     = $employeModel->countAll();

        return view('rh/dashboard', $this->viewData([
            'absences'        => $absences,
            'en_attente'      => $en_attente,
            'approuvees_mois' => $approuvees_mois,
            'nb_employes'     => $nb_employes,
        ]));
    }

    public function demandes()
    {
        if ($redirect = $this->requireRole('rh')) return $redirect;

        $congeModel       = new CongeModel();
        $departementModel = new DepartementModel();

        $statut       = $this->request->getGet('statut');
        $departementId = $this->request->getGet('departement_id');

        $demandes = $congeModel->getDemandesFiltrees(
            $statut ?: null,
            $departementId ? (int) $departementId : null
        );

        return view('rh/demandes', $this->viewData([
            'demandes'          => $demandes,
            'departements'      => $departementModel->findAll(),
            'filtre_statut'     => $statut,
            'filtre_departement' => $departementId,
        ]));
    }

    public function detail(int $id)
    {
        if ($redirect = $this->requireRole('rh')) return $redirect;

        $congeModel = new CongeModel();
        $demande    = $congeModel
            ->select('conges.*, employes.nom, employes.prenom, employes.departement_id,
                      types_conge.libelle as type_libelle, departements.nom as departement_nom')
            ->join('employes', 'employes.id = conges.employe_id')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->find($id);

        if (!$demande) {
            return redirect()->to('rh/demandes')
                ->with('error', 'Demande introuvable.');
        }

        return view('rh/detail', $this->viewData([
            'demande' => $demande,
        ]));
    }

    public function traiter(int $id)
    {
        if ($redirect = $this->requireRole('rh')) return $redirect;

        $congeModel = new CongeModel();
        $demande    = $congeModel->find($id);

        if (!$demande) {
            return redirect()->to('rh/demandes')
                ->with('error', 'Demande introuvable.');
        }

        $action    = $this->request->getPost('action');
        $commentaire = $this->request->getPost('commentaire_rh');

        if (!in_array($action, ['approuvee', 'refusee'])) {
            return redirect()->back()->with('error', 'Action invalide.');
        }

        if ($action === 'approuvee' && $demande['statut'] !== 'en_attente') {
            return redirect()->to('rh/demandes')
                ->with('error', 'Seules les demandes en attente peuvent être approuvées.');
        }

        if ($action === 'refusee' && !in_array($demande['statut'], ['en_attente', 'approuvee'])) {
            return redirect()->to('rh/demandes')
                ->with('error', 'Cette demande ne peut pas être refusée.');
        }

        if ($action === 'refusee' && $demande['statut'] === 'approuvee') {
            $this->recrediterSoldeSiNecessaire($demande);
        }

        $updateData = [
            'statut'        => $action,
            'traite_par'    => (int) $this->employe['id'],
            'commentaire_rh' => $commentaire ?: null,
        ];

        if ($action === 'approuvee') {
            $typeCongeId = (int) $demande['type_conge_id'];
            $annee       = (int) date('Y', strtotime($demande['date_debut']));
            $nbJours     = (int) $demande['nb_jours'];
            $employeId   = (int) $demande['employe_id'];

            $soldeModel = new SoldeModel();
            $typeCongeModel = new \App\Models\TypeCongeModel();
            $type = $typeCongeModel->find($typeCongeId);

            if ($type && (int) $type['deductible'] === 1) {
                if (!$soldeModel->deduireJours($employeId, $typeCongeId, $annee, $nbJours)) {
                    return redirect()->to('rh/demandes')
                        ->with('error', 'Impossible d\'approuver : solde insuffisant.');
                }
            }
        }

        $congeModel->update($id, $updateData);

        $msg = $action === 'approuvee'
            ? 'Demande approuvée avec succès.'
            : 'Demande refusée.';

        return redirect()->to('rh/demandes')->with('success', $msg);
    }

    public function soldes()
    {
        if ($redirect = $this->requireRole('rh')) return $redirect;

        $employeModel = new EmployeModel();
        $soldeModel   = new SoldeModel();

        $departementId = $this->request->getGet('departement_id');
        $annee         = $this->request->getGet('annee') ? (int) $this->request->getGet('annee') : (int) date('Y');

        $employes = $employeModel
            ->select('employes.*, departements.nom as departement_nom')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('employes.actif', 1);

        if ($departementId) {
            $employes->where('employes.departement_id', (int) $departementId);
        }

        $employes = $employes->orderBy('employes.nom', 'ASC')->findAll();

        $soldes = [];
        foreach ($employes as $emp) {
            $soldesEmp = $soldeModel
                ->select('soldes.*, types_conge.libelle,
                          (soldes.jours_attribues - soldes.jours_pris) as reste')
                ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
                ->where('soldes.employe_id', $emp['id'])
                ->where('soldes.annee', $annee)
                ->findAll();
            $soldes[$emp['id']] = $soldesEmp;
        }

        $departementModel = new DepartementModel();

        return view('rh/soldes', $this->viewData([
            'employes'       => $employes,
            'soldes'         => $soldes,
            'annee'          => $annee,
            'annees'         => range(date('Y') - 2, date('Y') + 1),
            'departements'   => $departementModel->findAll(),
            'filtre_departement' => $departementId,
        ]));
    }
}
