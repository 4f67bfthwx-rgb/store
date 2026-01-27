<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Ordini extends Migration
{
    public function up()
    {
        // 1. Resettiamo la tabella per sicurezza
        $this->forge->dropTable('ordini', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            
            // DATI CLIENTE
            'nome_cliente'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'indirizzo'     => ['type' => 'TEXT'],
            'citta'         => ['type' => 'VARCHAR', 'constraint' => 100],
            
            // DATI ORDINE
            'totale'        => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'dettagli_prodotti' => ['type' => 'JSON'], 
            
            // DATE (Queste sono quelle che cerca il Model)
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true], // <--- QUESTA MANCAVA!
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('ordini');
    }

    public function down()
    {
        $this->forge->dropTable('ordini');
    }
}