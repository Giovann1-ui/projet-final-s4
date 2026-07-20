<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMobileMoneyTables extends Migration
{
    public function up()
    {

        $this->forge->addField([
            'id'    => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'libelle'  => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'isUs' => ['type' => 'BOOLEAN', 'default' => false],
            'commission' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('operateur');

        // 1. Table prefixe_operateur
        $this->forge->addField([
            'id'    => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'code'  => ['type' => 'VARCHAR', 'constraint' => 5, 'unique' => true],
            'operateur_id' => ['type' => 'INTEGER', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('operateur_id', 'operateur', 'id', 'CASCADE', 'CASCADE');
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
            'frais_retrait'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'commission'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
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
