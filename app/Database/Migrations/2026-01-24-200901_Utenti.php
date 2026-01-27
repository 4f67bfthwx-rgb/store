<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Utenti extends Migration
{
    public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
        'nome' => ['type' => 'VARCHAR', 'constraint' => '100'],
        'email' => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => true],
        'password' => ['type' => 'VARCHAR', 'constraint' => '255'],
        'ruolo' => ['type' => 'ENUM', 'constraint' => ['admin', 'utente'], 'default' => 'utente'],
        'punti_fedelta' => ['type' => 'INT', 'default' => 0], // Saldo punti
        'created_at' => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('utenti');
}

public function down() { $this->forge->dropTable('utenti'); }
}
