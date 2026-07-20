<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // 1. Insertion des préfixes
        $this->db->table('prefixe_operateur')->insertBatch([
            ['code' => '033'],
            ['code' => '037']
        ]);

        // 2. Insertion des types d'opérations
        $this->db->table('type_operation')->insertBatch([
            ['id' => 1, 'libelle' => 'DEPOT'],
            ['id' => 2, 'libelle' => 'RETRAIT'],
            ['id' => 3, 'libelle' => 'TRANSFERT']
        ]);

        // 3. Insertion des clients de test (inscriptions fictives)
        $this->db->table('client')->insertBatch([
            ['id' => 1, 'num_tel' => '0331234567', 'date_inscription' => date('Y-m-d H:i:s')],
            ['id' => 2, 'num_tel' => '0379876543', 'date_inscription' => date('Y-m-d H:i:s')]
        ]);

        // 4. Barème des frais basé sur ton image (ID 2 = RETRAIT, ID 3 = TRANSFERT)
        $baremes = [
            // Tranches pour les retraits (type_operation = 2)
            ['type_operation' => 2, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 50],
            ['type_operation' => 2, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 50],
            ['type_operation' => 2, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 100],
            ['type_operation' => 2, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 200],
            ['type_operation' => 2, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 400],
            ['type_operation' => 2, 'montant_min' => 50001,  'montant_max' => 100000,  'frais' => 800],
            ['type_operation' => 2, 'montant_min' => 100001, 'montant_max' => 250500,  'frais' => 1500],
            
            // Tranches pour les transferts (type_operation = 3)
            ['type_operation' => 3, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 50],
            ['type_operation' => 3, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 50],
            ['type_operation' => 3, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 100],
            ['type_operation' => 3, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 200],
            ['type_operation' => 3, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 400],
        ];

        $this->db->table('bareme_frais')->insertBatch($baremes);
        
        // 5. Simulation d'un dépôt initial pour le client 1 pour qu'il ait du solde (Ex: 50 000 Ar)
        $this->db->table('transaction')->insert([
            'type_operation' => 1, // DEPOT
            'client_source'  => null,
            'client_dest'    => 1, // Client 1
            'montant_brut'   => 50000,
            'frais'          => 0,
            'montant_net'    => 50000,
            'date'           => date('Y-m-d H:i:s')
        ]);
    }
}