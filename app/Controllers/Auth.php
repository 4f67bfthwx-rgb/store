<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UtentiModel;

class Auth extends BaseController
{
    // -------------------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------------------
    
    // 1. Mostra il form di login
    public function login()
    {
        return view('auth/login');
    }

    // 2. Elabora i dati del login
    public function loginAuth()
    {
        $session = session();
        $model = new UtentiModel();
        
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        
        // Cerca l'utente nel database
        $data = $model->where('email', $email)->first();

        if ($data) {
            // Verifica la password criptata
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            
            if ($verify_pass) {
                // Password corretta: Creiamo la sessione
                $ses_data = [
                    'id'            => $data['id'],
                    'nome'          => $data['nome'],
                    'email'         => $data['email'],
                    'ruolo'         => $data['ruolo'],
                    'punti_fedelta' => $data['punti_fedelta'], 
                    'isLoggedIn'    => true
                ];
                
                $session->set($ses_data);
                
                // Redirect in base al ruolo
                if($data['ruolo'] == 'admin'){
                    return redirect()->to('/admin/dashboard');
                } else {
                    return redirect()->to('/');
                }

            } else {
                $session->setFlashdata('msg', 'Password errata.');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('msg', 'Email non trovata.');
            return redirect()->to('/login');
        }
    }

    // -------------------------------------------------------------------------
    // REGISTRAZIONE (Pubblica)
    // -------------------------------------------------------------------------

    // 1. Mostra il form di registrazione
    public function register()
    {
        helper(['form']);
        return view('auth/register');
    }

    // 2. Salva il nuovo utente 
    // 2. Salva il nuovo utente 
    public function store()
    {
        // 1. Definiamo le regole
        $rules = [
            'nome'          => 'required|min_length[2]|max_length[50]',
            'email'         => 'required|min_length[4]|max_length[100]|valid_email|is_unique[utenti.email]',
            'password'      => 'required|min_length[4]|max_length[255]',
            'confpassword'  => 'matches[password]'
        ];

        // 2. Definiamo i messaggi in ITALIANO
        $messaggi = [
            'nome' => [
                'required'   => 'Il nome è obbligatorio.',
                'min_length' => 'Il nome deve contenere almeno 2 caratteri.',
                'max_length' => 'Il nome è troppo lungo.'
            ],
            'email' => [
                'required'    => 'L\'email è obbligatoria.',
                'valid_email' => 'Inserisci un indirizzo email valido.',
                'is_unique'   => 'Questa email è già registrata nel sistema.',
                'min_length'  => 'L\'email è troppo corta.'
            ],
            'password' => [
                'required'   => 'La password è obbligatoria.',
                'min_length' => 'La password deve avere almeno 4 caratteri.'
            ],
            'confpassword' => [
                'matches' => 'Le due password non coincidono.'
            ]
        ];

        // 3. Validiamo passando anche i messaggi tradotti
        if (! $this->validate($rules, $messaggi)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $utentiModel = new \App\Models\UtentiModel();

        // Contiamo gli utenti
        $numeroUtenti = $utentiModel->countAll();

        // Decidiamo il ruolo (Admin se è il primo, User gli altri)
        $ruoloAssegnato = ($numeroUtenti == 0) ? 'admin' : 'user';

        $data = [
            'nome'     => $this->request->getVar('nome'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'ruolo'    => $ruoloAssegnato 
        ];

        $utentiModel->save($data);

        // Messaggio di successo
        if ($ruoloAssegnato == 'admin') {
            return redirect()->to('/login')->with('success', 'Registrazione completata! Sei il Primo Utente (ADMIN).');
        } else {
            return redirect()->to('/login')->with('success', 'Registrazione completata! Ora puoi accedere.');
        }
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }
}