<?php

namespace App\Controllers;

use App\Models\OrdiniModel;
use App\Models\UtentiModel;

class Profile extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $emailUtente = session()->get('email');
        $userId      = session()->get('id');

        $utentiModel = new UtentiModel();
        $user = $utentiModel->find($userId);

        $ordiniModel = new OrdiniModel();
        $mieiOrdini = $ordiniModel->where('email', $emailUtente)
                                  ->orderBy('created_at', 'DESC')
                                  ->findAll();

        return view('profile/index', [
            'user'   => $user,
            'ordini' => $mieiOrdini
        ]);
    }
}