<?php

namespace App\Controllers;

use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $session;
    protected $employe;
    protected $role;

    protected $helpers = ['calendrier'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
        $this->employe = $this->session->get('employe');
        $this->role    = $this->employe['role'] ?? null;
    }

    protected function viewData(array $data = []): array
    {
        return array_merge([
            'employe' => $this->employe,
            'role'    => $this->role,
        ], $data);
    }

    protected function requireRole(string $role)
    {
        if ($this->role !== $role) {
            return redirect()->to('/')
                ->with('error', 'Accès non autorisé.');
        }
    }

    protected function recrediterSoldeSiNecessaire(array $demande): void
    {
        if ($demande['statut'] !== 'approuvee') {
            return;
        }

        $typeCongeModel = new TypeCongeModel();
        $type = $typeCongeModel->find((int) $demande['type_conge_id']);

        if (!$type || (int) $type['deductible'] !== 1) {
            return;
        }

        $soldeModel = new SoldeModel();
        $soldeModel->crediterJours(
            (int) $demande['employe_id'],
            (int) $demande['type_conge_id'],
            (int) date('Y', strtotime($demande['date_debut'])),
            (int) $demande['nb_jours']
        );
    }
}
