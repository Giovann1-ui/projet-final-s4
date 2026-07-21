<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoldeToEpargne extends Migration
{
    public function up()
    {
        $this->forge->addColumn('epargne', [
            'solde' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00, 'after' => 'pourcentage'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('epargne', 'solde');
    }
}
