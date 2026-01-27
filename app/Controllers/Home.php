<?php

namespace App\Controllers;

use App\Models\ProdottiModel;
use App\Models\GalleriaModel;

class Home extends BaseController
{
    public function index()
    {
        // SE L'UTENTE È UN ADMIN, LO DISCONNETTIAMO SE PROVA AD ENTRARE NELLA HOME CLIENTE
        if (session()->get('ruolo') == 'admin') {
            session()->destroy();
            return redirect()->to('/login')->with('msg', 'Accesso negato: gli amministratori non possono navigare la Home cliente.');
        }

        $model = new ProdottiModel();
        $data['prodotti'] = $model->findAll();

        return view('home', $data);
    }

    public function dettaglio($id)
{
    // MODIFICA: Permettiamo all'admin di VEDERE la pagina, 
    // ma manteniamo il logout solo se prova ad andare sulla Home generica (index).
    
    // Rimuoviamo il blocco che avevamo messo qui (session()->destroy())

    $model = new \App\Models\ProdottiModel();
    $galleriaModel = new \App\Models\GalleriaModel();

    $data['prodotto'] = $model->find($id);
    $data['galleria'] = $galleriaModel->where('prodotto_id', $id)->findAll();

    if (!$data['prodotto']) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    return view('dettaglio', $data);
}
}