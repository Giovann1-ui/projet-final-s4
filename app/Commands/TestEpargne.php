<?php

namespace App\Commands;

use App\Libraries\FraisService;
use App\Models\EpargneModel;
use App\Models\OperationModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestEpargne extends BaseCommand
{
    protected $group       = 'test';
    protected $name        = 'test:epargne';
    protected $description = 'Smoke test epargne deduction';

    public function run(array $params)
    {
        $epargneModel = new EpargneModel();
        $epargneModel->updatePourcentage(2, 10.0);

        $before = $epargneModel->getSolde(2);
        $soldeAvant = (new OperationModel())->getSoldeTotal(2);

        $op = (new FraisService())->transferer(1, '0349876543', 10000, false);

        $after = $epargneModel->getSolde(2);
        $soldeApres = (new OperationModel())->getSoldeTotal(2);

        CLI::write('montant_entrant operation: ' . $op['montant_entrant']);
        CLI::write('epargne avant: ' . $before . ' / apres: ' . $after);
        CLI::write('solde client2 avant: ' . $soldeAvant . ' / apres: ' . $soldeApres);
    }
}
