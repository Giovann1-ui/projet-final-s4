<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PrefixeBaremeSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('prefixe_operateur')->countAllResults() === 0) {
            $this->db->table('prefixe_operateur')->insertBatch([
                ['code' => '033'],
                ['code' => '037'],
            ]);
        }

        if ($this->db->table('bareme_frais')->countAllResults() === 0) {
            $this->db->table('bareme_frais')->insertBatch([
                ['type_operation' => 2, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 50],
                ['type_operation' => 2, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 50],
                ['type_operation' => 2, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 100],
                ['type_operation' => 2, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 200],
                ['type_operation' => 2, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 400],
                ['type_operation' => 2, 'montant_min' => 50010,  'montant_max' => 100000,  'frais' => 800],
                ['type_operation' => 2, 'montant_min' => 100001, 'montant_max' => 250500,  'frais' => 1500],

                ['type_operation' => 3, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 50],
                ['type_operation' => 3, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 50],
                ['type_operation' => 3, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 100],
                ['type_operation' => 3, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 200],
                ['type_operation' => 3, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 400],
            ]);
        }
    }
}
