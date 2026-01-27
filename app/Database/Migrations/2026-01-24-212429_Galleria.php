<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Galleria extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'prodotto_id' => ['type' => 'INT', 'unsigned' => true], // Collegamento al prodotto
            'foto' => ['type' => 'LONGBLOB'], // Dati binari della foto
            'foto_type' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('prodotto_id', 'prodotti', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('galleria');
    }

    public function down() { $this->forge->dropTable('galleria'); }
}
