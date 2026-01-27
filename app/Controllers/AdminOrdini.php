<?php

namespace App\Controllers;

use App\Models\OrdiniModel;

class AdminOrdini extends BaseController
{
    public function index()
    {
        if (session()->get('ruolo') != 'admin') {
            return redirect()->to('/');
        }

        $ordiniModel = new OrdiniModel();
        $ordini = $ordiniModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/ordini', ['ordini' => $ordini]);
    }

    // NUOVA FUNZIONE PER IL CAMBIO STATO
    public function cambia_stato($id, $nuovo_stato)
    {
        if (session()->get('ruolo') != 'admin') return redirect()->to('/');

        $ordiniModel = new OrdiniModel();
        
        // Verifichiamo che l'ordine esista
        if ($ordiniModel->find($id)) {
            $ordiniModel->update($id, ['stato' => $nuovo_stato]);
            return redirect()->to('admin/ordini')->with('msg', "Stato ordine #$id aggiornato!");
        }

        return redirect()->to('admin/ordini')->with('msg', "Errore nell'aggiornamento.");
    }
}