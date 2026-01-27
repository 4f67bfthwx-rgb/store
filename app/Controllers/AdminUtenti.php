<?php

namespace App\Controllers;

use App\Models\UtentiModel;

class AdminUtenti extends BaseController
{
    // 1. Mostra il form per creare un nuovo Admin
    public function nuovo()
    {
        // Protezione
        if (session()->get('ruolo') != 'admin') {
            return redirect()->to('/');
        }

        return view('admin/crea_admin');
    }

    // 2. Salva il nuovo Admin nel database
    public function crea()
    {
        if (session()->get('ruolo') != 'admin') {
            return redirect()->to('/');
        }

        $utentiModel = new UtentiModel();

        // Recuperiamo i dati
        $dati = [
            'nome'     => $this->request->getPost('nome'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'ruolo'    => 'admin' // <--- Qui forziamo 'admin' perché siamo nel pannello protetto
        ];

        // Salviamo
        $utentiModel->save($dati);

        return redirect()->to('admin/dashboard')->with('messaggio', 'Nuovo Admin creato con successo!');
    }
}