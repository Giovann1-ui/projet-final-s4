<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // 1. Insertion des opérateurs et de leurs préfixes
        if ($this->db->table('operateur')->countAllResults() === 0) {
            $this->db->table('operateur')->insertBatch([
                ['id' => 1, 'libelle' => 'YAS', 'isUs' => true, 'commission' => 0.00],
                ['id' => 2, 'libelle' => 'Airtel', 'isUs' => false, 'commission' => 10.00],
            ]);
        }

        if ($this->db->table('prefixe_operateur')->countAllResults() === 0) {
            $this->db->table('prefixe_operateur')->insertBatch([
                ['code' => '034', 'operateur_id' => 1],
                ['code' => '038', 'operateur_id' => 1],
                ['code' => '033', 'operateur_id' => 2],
            ]);
        }

        // 2. Insertion des types d'opérations
        if ($this->db->table('type_operation')->countAllResults() === 0) {
            $this->db->table('type_operation')->insertBatch([
                ['id' => 1, 'libelle' => 'DEPOT'],
                ['id' => 2, 'libelle' => 'RETRAIT'],
                ['id' => 3, 'libelle' => 'TRANSFERT']
            ]);
        }

        // 3. Insertion des clients de test (inscriptions fictives)
        if ($this->db->table('client')->countAllResults() === 0) {
            $this->db->table('client')->insertBatch([
                ['id' => 1, 'num_tel' => '0341234567', 'date_inscription' => date('Y-m-d H:i:s')],
                ['id' => 2, 'num_tel' => '0349876543', 'date_inscription' => date('Y-m-d H:i:s')],
                ['id' => 3, 'num_tel' => '0337654321', 'date_inscription' => date('Y-m-d H:i:s')],
                ['id' => 4, 'num_tel' => '0331234567', 'date_inscription' => date('Y-m-d H:i:s')],
            ]);
        }

        // 4. Barème des frais basé sur ton image (ID 2 = RETRAIT, ID 3 = TRANSFERT)
        $baremes = [
            // Tranches pour les retraits (type_operation = 2)
            ['type_operation' => 2, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 50],
            ['type_operation' => 2, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 50],
            ['type_operation' => 2, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 100],
            ['type_operation' => 2, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 200],
            ['type_operation' => 2, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 400],
            ['type_operation' => 2, 'montant_min' => 50010,  'montant_max' => 100000,  'frais' => 800],
            ['type_operation' => 2, 'montant_min' => 100001, 'montant_max' => 250500,  'frais' => 1500],

            // Tranches pour les transferts (type_operation = 3)
            ['type_operation' => 3, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 50],
            ['type_operation' => 3, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 50],
            ['type_operation' => 3, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 100],
            ['type_operation' => 3, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 200],
            ['type_operation' => 3, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 400],
        ];

        if ($this->db->table('bareme_frais')->countAllResults() === 0) {
            $this->db->table('bareme_frais')->insertBatch($baremes);
        }

        // 5. Simulation d'un dépôt initial pour le client 1 (Correction table 'operation' et colonnes montants)

        $operation = [
            [
                'type_operation'  => 1, // DEPOT
                'client_source'   => null,
                'client_dest'     => 1, // Client 1
                'montant_brut'    => 50000,
                'frais'           => 0,
                'montant_entrant' => 50000,
                'montant_sortant' => 0,
                'date'            => date('Y-m-d H:i:s')
            ],
            [
                'type_operation'  => 2, // RETRAIT
                'client_source'   => 2,
                'client_dest'     => NULL, // Client 2
                'montant_brut'    => 30000,
                'frais'           => 100,
                'montant_entrant' => 0,
                'montant_sortant' => 31000,
                'date'            => date('Y-m-d H:i:s')
            ],
            [
                'type_operation'  => 3, // TRANSFERT
                'client_source'   => 1,
                'client_dest'     => 2,
                'montant_brut'    => 20000,
                'frais'           => 1000,
                'montant_entrant' => 20000,
                'montant_sortant' => 21000,
                'date'            => date('Y-m-d H:i:s')
            ],
            [
                'type_operation'  => 3, // TRANSFERT
                'client_source'   => 2,
                'client_dest'     => 1,
                'montant_brut'    => 15000,
                'frais'           => 500,
                'montant_entrant' => 15000,
                'montant_sortant' => 15500,
                'date'            => date('Y-m-d H:i:s')
            ]
        ];
        if ($this->db->table('operation')->countAllResults() === 0) {
            $this->db->table('operation')->insertBatch(array_map(static function (array $row): array {
                $row['frais_retrait'] = $row['type_operation'] === 2 ? 1000 : 0;
                $row['commission'] = 0.00;

                return $row;
            }, $operation));
        }
    }
}
