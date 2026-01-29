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
                    
                    // --- NOVITÀ: Salviamo i punti in sessione ---
                    'punti_fedelta' => $data['punti_fedelta'], 
                    // --------------------------------------------
                    
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
    public function store()
    {
        helper(['form']);
        
        // Definizione delle regole con messaggi personalizzati
        $rules = [
            'nome' => 'required|min_length[3]|max_length[50]',
            
            'email' => [
                'rules'  => 'required|min_length[6]|max_length[100]|valid_email|is_unique[utenti.email]',
                'errors' => [
                    'is_unique'   => 'Questa email è già registrata nel sistema.',
                    'valid_email' => 'Inserisci un indirizzo email valido.'
                ]
            ],
            
            'password' => 'required|min_length[4]|max_length[255]',
            
            'confirmpassword' => [
                'rules'  => 'matches[password]',
                'errors' => [
                    'matches' => 'Le due password non coincidono.' 
                ]
            ]
        ];

        if ($this->validate($rules)) {
            $model = new UtentiModel();
            
            $data = [
                'nome'     => $this->request->getVar('nome'),
                'email'    => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                'ruolo'    => 'user' // Default user per registrazioni pubbliche
            ];
            
            $model->save($data);
            
            // Redirect al login con messaggio di successo
            return redirect()->to('/login')->with('msg', 'Registrazione completata! Ora puoi accedere.');
        } else {
            // Se c'è errore, ricarica la pagina mostrando gli errori specifici
            $data['validation'] = $this->validator;
            return view('auth/register', $data);
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