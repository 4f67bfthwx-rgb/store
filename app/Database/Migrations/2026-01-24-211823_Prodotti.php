<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Prodotti extends Migration
{
    public function up()
    {
        // Resettiamo la tabella se esiste già per evitare conflitti
        $this->forge->dropTable('prodotti', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'descrizione' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'prezzo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            // IL MAGAZZINO (JSON)
            'magazzino' => [
                'type' => 'JSON', 
                'null' => true
            ], 
            // VESTIBILITA' (Il campo che ti mancava)
            'vestibilita' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            // FOTO
            'immagine' => [
                'type' => 'LONGBLOB',
                'null' => true,
            ],
            'immagine_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('prodotti');
    }

    public function down()
    {
        $this->forge->dropTable('prodotti');
    }
}