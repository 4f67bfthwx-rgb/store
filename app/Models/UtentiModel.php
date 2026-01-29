<?php

namespace App\Models;

use CodeIgniter\Model;

class UtentiModel extends Model
{
    protected $table = 'utenti';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'nome', 
        'email', 
        'password', 
        'ruolo', 
        'created_at',
        'punti_fedelta'
    ];
}

