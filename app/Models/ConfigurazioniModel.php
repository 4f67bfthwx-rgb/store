<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigurazioniModel extends Model
{
    protected $table = 'configurazioni';
    protected $primaryKey = 'id';
    
    // Questi sono i campi che possiamo scrivere
    protected $allowedFields = ['chiave', 'valore'];
    
    protected $returnType = 'array';
}