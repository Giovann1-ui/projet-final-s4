<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class Admin extends BaseController
{
    public function index()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $congeModel       = new CongeModel();
        $employeModel     = new EmployeModel();
        $departementModel = new DepartementModel();

        $mois     = date('n');
        $annee    = date('Y');
        $absences = $congeModel->getAbsencesMois($mois, $annee);
        $congesParMois = $congeModel->getCongesParMois((int) $annee);
        $congesApprouves = $congeModel->getCongesApprouvesAnnee((int) $annee);

        $seriesParMois = array_fill(1, 12, 0);
        foreach ($congesParMois as $row) {
            $moisIndex = (int) $row['mois'];
            $seriesParMois[$moisIndex] = (int) $row['total'];
        }

        $statsJours = $this->buildWeekdayStats($congesApprouves);

        return view('admin/dashboard', $this->viewData([
            'absences'        => $absences,
            'nb_employes'     => $employeModel->countAll(),
            'nb_departements' => $departementModel->countAll(),
            'en_attente'      => count($congeModel->getDemandesEnAttente()),
            'absences_mois'   => count($absences),
            'conges_par_mois' => array_values($seriesParMois),
            'jours_labels'    => $statsJours['labels'],
            'jours_counts'    => $statsJours['counts'],
            'annee_stats'     => (int) $annee,
        ]));
    }

    private function buildWeekdayStats(array $conges): array
    {
        $labels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
        $counts = array_fill(0, 5, 0);

        foreach ($conges as $c) {
            $start = new \DateTime($c['date_debut']);
            $end = new \DateTime($c['date_fin']);
            $end->modify('+1 day');

            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
            foreach ($period as $day) {
                $weekday = (int) $day->format('N');
                if ($weekday <= 5) {
                    $counts[$weekday - 1]++;
                }
            }
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }

    // ─── Employés ────────────────────────────────────────────────────────────

    public function employes()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $employeModel = new EmployeModel();
        $employes     = $employeModel
            ->select('employes.*, departements.nom as departement_nom')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->orderBy('employes.date_embauche', 'DESC')
            ->findAll();

        return view('admin/employes/index', $this->viewData([
            'employes' => $employes,
        ]));
    }

    public function employeCreer()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $departementModel = new DepartementModel();

        return view('admin/employes/form', $this->viewData([
            'employe'     => null,
            'departements' => $departementModel->findAll(),
            'validation'  => null,
        ]));
    }

    public function employeStore()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $employeModel = new EmployeModel();

        if (!$employeModel->save($this->request->getPost())) {
            $departementModel = new DepartementModel();
            return view('admin/employes/form', $this->viewData([
                'employe'     => null,
                'departements' => $departementModel->findAll(),
                'validation'  => $employeModel->errors(),
            ]));
        }

        return redirect()->to('admin/employes')
            ->with('success', 'Employé créé avec succès.');
    }

    public function employeEditer(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $employeModel     = new EmployeModel();
        $departementModel = new DepartementModel();
        $employe          = $employeModel->find($id);

        if (!$employe) {
            return redirect()->to('admin/employes')
                ->with('error', 'Employé introuvable.');
        }

        return view('admin/employes/form', $this->viewData([
            'employe'      => $employe,
            'departements' => $departementModel->findAll(),
            'validation'   => null,
        ]));
    }

    public function employeUpdate(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $employeModel = new EmployeModel();
        $employe      = $employeModel->find($id);

        if (!$employe) {
            return redirect()->to('admin/employes')
                ->with('error', 'Employé introuvable.');
        }

        $data = $this->request->getPost();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['id'] = $id;

        if (!$employeModel->save($data)) {
            $departementModel = new DepartementModel();
            return view('admin/employes/form', $this->viewData([
                'employe'      => $employe,
                'departements' => $departementModel->findAll(),
                'validation'   => $employeModel->errors(),
            ]));
        }

        return redirect()->to('admin/employes')
            ->with('success', 'Employé mis à jour avec succès.');
    }

    public function employeToggle(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $employeModel = new EmployeModel();
        $employe      = $employeModel->find($id);

        if (!$employe) {
            return redirect()->to('admin/employes')
                ->with('error', 'Employé introuvable.');
        }

        $nouveauStatut = $employe['actif'] ? 0 : 1;
        $employeModel->update($id, ['actif' => $nouveauStatut]);

        $msg = $nouveauStatut ? 'Employé activé.' : 'Employé désactivé.';
        return redirect()->to('admin/employes')->with('success', $msg);
    }

    // ─── Départements ────────────────────────────────────────────────────────

    public function departements()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $departementModel = new DepartementModel();
        $employeModel     = new EmployeModel();
        $departements     = $departementModel->findAll();

        $nbEmployes = [];
        foreach ($departements as $d) {
            $nbEmployes[$d['id']] = $employeModel->where('departement_id', $d['id'])->countAllResults();
        }

        return view('admin/departements/index', $this->viewData([
            'departements' => $departements,
            'nbEmployes'   => $nbEmployes,
        ]));
    }

    public function departementCreer()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('admin/departements/form', $this->viewData([
            'departement' => null,
            'validation'  => null,
        ]));
    }

    public function departementStore()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $departementModel = new DepartementModel();

        if (!$departementModel->save($this->request->getPost())) {
            return view('admin/departements/form', $this->viewData([
                'departement' => null,
                'validation'  => $departementModel->errors(),
            ]));
        }

        return redirect()->to('admin/departements')
            ->with('success', 'Département créé avec succès.');
    }

    public function departementEditer(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $departementModel = new DepartementModel();
        $departement      = $departementModel->find($id);

        if (!$departement) {
            return redirect()->to('admin/departements')
                ->with('error', 'Département introuvable.');
        }

        return view('admin/departements/form', $this->viewData([
            'departement' => $departement,
            'validation'  => null,
        ]));
    }

    public function departementUpdate(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $departementModel = new DepartementModel();
        $departement      = $departementModel->find($id);

        if (!$departement) {
            return redirect()->to('admin/departements')
                ->with('error', 'Département introuvable.');
        }

        $data       = $this->request->getPost();
        $data['id'] = $id;

        if (!$departementModel->save($data)) {
            return view('admin/departements/form', $this->viewData([
                'departement' => $departement,
                'validation'  => $departementModel->errors(),
            ]));
        }

        return redirect()->to('admin/departements')
            ->with('success', 'Département mis à jour avec succès.');
    }

    public function departementSupprimer(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $departementModel = new DepartementModel();
        $employeModel     = new EmployeModel();
        $departement      = $departementModel->find($id);

        if (!$departement) {
            return redirect()->to('admin/departements')
                ->with('error', 'Département introuvable.');
        }

        $nbEmployes = $employeModel->where('departement_id', $id)->countAllResults();
        if ($nbEmployes > 0) {
            return redirect()->to('admin/departements')
                ->with('error', 'Impossible de supprimer : ' . $nbEmployes . ' employé(s) y sont rattachés.');
        }

        $departementModel->delete($id);

        return redirect()->to('admin/departements')
            ->with('success', 'Département supprimé.');
    }

    // ─── Types de congé ──────────────────────────────────────────────────────

    public function typesConge()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $typeCongeModel = new TypeCongeModel();

        return view('admin/types_conge/index', $this->viewData([
            'types' => $typeCongeModel->findAll(),
        ]));
    }

    public function typeCongeCreer()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('admin/types_conge/form', $this->viewData([
            'type'       => null,
            'validation' => null,
        ]));
    }

    public function typeCongeStore()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $typeCongeModel = new TypeCongeModel();

        if (!$typeCongeModel->save($this->request->getPost())) {
            return view('admin/types_conge/form', $this->viewData([
                'type'       => null,
                'validation' => $typeCongeModel->errors(),
            ]));
        }

        return redirect()->to('admin/types-conge')
            ->with('success', 'Type de congé créé avec succès.');
    }

    public function typeCongeEditer(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $typeCongeModel = new TypeCongeModel();
        $type           = $typeCongeModel->find($id);

        if (!$type) {
            return redirect()->to('admin/types-conge')
                ->with('error', 'Type de congé introuvable.');
        }

        return view('admin/types_conge/form', $this->viewData([
            'type'       => $type,
            'validation' => null,
        ]));
    }

    public function typeCongeUpdate(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $typeCongeModel = new TypeCongeModel();
        $type           = $typeCongeModel->find($id);

        if (!$type) {
            return redirect()->to('admin/types-conge')
                ->with('error', 'Type de congé introuvable.');
        }

        $data       = $this->request->getPost();
        $data['id'] = $id;

        if (!$typeCongeModel->save($data)) {
            return view('admin/types_conge/form', $this->viewData([
                'type'       => $type,
                'validation' => $typeCongeModel->errors(),
            ]));
        }

        return redirect()->to('admin/types-conge')
            ->with('success', 'Type de congé mis à jour avec succès.');
    }

    public function typeCongeSupprimer(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $typeCongeModel = new TypeCongeModel();
        $congeModel     = new CongeModel();
        $type           = $typeCongeModel->find($id);

        if (!$type) {
            return redirect()->to('admin/types-conge')
                ->with('error', 'Type de congé introuvable.');
        }

        $nbDemandes = $congeModel->where('type_conge_id', $id)->countAllResults();
        if ($nbDemandes > 0) {
            return redirect()->to('admin/types-conge')
                ->with('error', 'Impossible de supprimer : ' . $nbDemandes . ' demande(s) utilisent ce type.');
        }

        $typeCongeModel->delete($id);

        return redirect()->to('admin/types-conge')
            ->with('success', 'Type de congé supprimé.');
    }

    // ─── Demandes ────────────────────────────────────────────────────────────

    public function demandes()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $congeModel       = new CongeModel();
        $departementModel = new DepartementModel();

        $statut        = $this->request->getGet('statut');
        $departementId = $this->request->getGet('departement_id');

        $demandes = $congeModel->getDemandesFiltrees(
            $statut ?: null,
            $departementId ? (int) $departementId : null
        );

        return view('admin/demandes/index', $this->viewData([
            'demandes'           => $demandes,
            'departements'       => $departementModel->findAll(),
            'filtre_statut'      => $statut,
            'filtre_departement' => $departementId,
        ]));
    }

    // ─── Soldes annuels ──────────────────────────────────────────────────────

    public function soldesCreer()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $employeModel     = new EmployeModel();
        $typeCongeModel   = new TypeCongeModel();

        return view('admin/soldes/form', $this->viewData([
            'solde'      => null,
            'employes'   => $employeModel->where('actif', 1)->orderBy('nom', 'ASC')->findAll(),
            'types'      => $typeCongeModel->findAll(),
            'annee'      => date('Y'),
            'validation' => null,
        ]));
    }

    public function soldesStore()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $soldeModel = new SoldeModel();
        $employeModel     = new EmployeModel();
        $typeCongeModel   = new TypeCongeModel();

        $data = $this->request->getPost();

        $existant = $soldeModel->getSolde($data['employe_id'], $data['type_conge_id'], $data['annee']);
        if ($existant) {
            return view('admin/soldes/form', $this->viewData([
                'solde'      => null,
                'employes'   => $employeModel->where('actif', 1)->orderBy('nom', 'ASC')->findAll(),
                'types'      => $typeCongeModel->findAll(),
                'annee'      => $data['annee'] ?? date('Y'),
                'validation' => ['Un solde existe déjà pour cet employé, ce type et cette année.'],
            ]));
        }

        if (!$soldeModel->save($data)) {
            return view('admin/soldes/form', $this->viewData([
                'solde'      => null,
                'employes'   => $employeModel->where('actif', 1)->orderBy('nom', 'ASC')->findAll(),
                'types'      => $typeCongeModel->findAll(),
                'annee'      => $data['annee'] ?? date('Y'),
                'validation' => $soldeModel->errors(),
            ]));
        }

        return redirect()->to('admin/soldes?annee=' . $data['annee'])
            ->with('success', 'Solde créé avec succès.');
    }

    public function soldes()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $soldeModel = new SoldeModel();
        $annee      = $this->request->getGet('annee') ? (int) $this->request->getGet('annee') : (int) date('Y');

        $soldes = $soldeModel
            ->select('soldes.*, employes.nom, employes.prenom, types_conge.libelle,
                      (soldes.jours_attribues - soldes.jours_pris) as reste')
            ->join('employes', 'employes.id = soldes.employe_id')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('soldes.annee', $annee)
            ->orderBy('employes.nom', 'ASC')
            ->findAll();

        return view('admin/soldes/index', $this->viewData([
            'soldes' => $soldes,
            'annee'  => $annee,
            'annees' => range(date('Y') - 2, date('Y') + 1),
        ]));
    }

    public function soldesEditer(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $soldeModel = new SoldeModel();
        $solde      = $soldeModel
            ->select('soldes.*, employes.nom, employes.prenom, types_conge.libelle')
            ->join('employes', 'employes.id = soldes.employe_id')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->find($id);

        if (!$solde) {
            return redirect()->to('admin/soldes')
                ->with('error', 'Solde introuvable.');
        }

        return view('admin/soldes/form', $this->viewData([
            'solde'      => $solde,
            'validation' => null,
        ]));
    }

    public function soldesUpdate(int $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $soldeModel = new SoldeModel();
        $solde      = $soldeModel->find($id);

        if (!$solde) {
            return redirect()->to('admin/soldes')
                ->with('error', 'Solde introuvable.');
        }

        $data = [
            'id'              => $id,
            'jours_attribues' => (int) $this->request->getPost('jours_attribues'),
            'jours_pris'      => (int) $this->request->getPost('jours_pris'),
        ];

        if (!$soldeModel->save($data)) {
            return view('admin/soldes/form', $this->viewData([
                'solde'      => $solde,
                'validation' => $soldeModel->errors(),
            ]));
        }

        $annee = $this->request->getPost('annee') ?: $solde['annee'];
        return redirect()->to('admin/soldes?annee=' . $annee)
            ->with('success', 'Solde mis à jour avec succès.');
    }

    // ─── Historique ──────────────────────────────────────────────────────────

    public function historique()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $congeModel = new CongeModel();
        $historique = $congeModel->getHistoriqueComplet();

        return view('admin/historique', $this->viewData([
            'historique' => $historique,
        ]));
    }
}
