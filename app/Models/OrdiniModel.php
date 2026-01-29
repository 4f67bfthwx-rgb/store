<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdiniModel extends Model
{
    protected $table = 'ordini';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'nome_cliente', 
        'email', 
        'indirizzo', 
        'citta', 
        'totale', 
        'dettagli_prodotti', 
        'stato', 
        'created_at',
        'omaggi' 
    ];
    
    protected $useTimestamps = true;
    protected array $casts = []; 
}