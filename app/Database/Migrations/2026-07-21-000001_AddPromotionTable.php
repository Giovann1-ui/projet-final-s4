<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPromotionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'pourcentage' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('promotion');

        // Ligne unique de configuration (pourcentage de reduction de frais, meme operateur uniquement)
        $this->db->table('promotion')->insert(['id' => 1, 'pourcentage' => 0.00]);

        $this->forge->addField([
            'id'          => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
            'pourcentage' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'client_id' => ['type' => 'INTEGER', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'client', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('epargne');
    }

    public function down()
    {
        $this->forge->dropTable('promotion', true);
        $this->forge->dropTable('epargne' ,true);
    }

}
