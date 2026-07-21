<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMobileMoneyTables extends Migration
{
    public function up()
    {
        // 1. Table prefixe_operateur
        $this->forge->addField([
            'id'    => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'code'  => ['type' => 'VARCHAR', 'constraint' => 5, 'unique' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('prefixe_operateur');

        // 2. Table client
        $this->forge->addField([
            'id'               => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'num_tel'          => ['type' => 'VARCHAR', 'constraint' => 15, 'unique' => true],
            'date_inscription' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('client');

        // 3. Table type_operation
        $this->forge->addField([
            'id'      => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'libelle' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('type_operation');

        // 4. Table bareme_frais
        $this->forge->addField([
            'id'             => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'type_operation' => ['type' => 'INTEGER', 'constraint' => 11],
            'montant_min'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'montant_max'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'frais'          => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ]);
        $this->forge->addKey('id', true);
        // SQLite supporte la syntaxe de clé étrangère au moment du create via forge
        $this->forge->addForeignKey('type_operation', 'type_operation', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bareme_frais');

        // 5. Table operation
        $this->forge->addField([
            'id'             => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'type_operation' => ['type' => 'INTEGER', 'constraint' => 11],
            'client_source'  => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'client_dest'    => ['type' => 'INTEGER', 'constraint' => 11, 'null' => true],
            'montant_brut'   => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'frais'          => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'montant_entrant' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'montant_sortant' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'date'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('type_operation', 'type_operation', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('client_source', 'client', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('client_dest', 'client', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('operation');

        
    }

    public function down()
    {
        $this->forge->dropTable('operation', true);
        $this->forge->dropTable('bareme_frais', true);
        $this->forge->dropTable('type_operation', true);
        $this->forge->dropTable('client', true);
        $this->forge->dropTable('prefixe_operateur', true);
    }
}


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
            ['id' => 2, 'num_tel' => '0379876543', 'date_inscription' => date('Y-m-d H:i:s')],
            ['id' => 3, 'num_tel' => '0337654321', 'date_inscription' => date('Y-m-d H:i:s')],
            ['id' => 4, 'num_tel' => '0371234567', 'date_inscription' => date('Y-m-d H:i:s')],
        ]);

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

        $this->db->table('bareme_frais')->insertBatch($baremes);

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
        $this->db->table('operation')->insertBatch($operation);
    }
}

