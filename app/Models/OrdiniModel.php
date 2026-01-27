<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdiniModel extends Model
{
    protected $table = 'ordini';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'nome_cliente', 'email', 'indirizzo', 'citta', 
        'totale', 'dettagli_prodotti', 'stato', 'created_at'
    ];
    
    protected $useTimestamps = true;

    // Assicurati che non ci sia nulla in $casts che riguardi dettagli_prodotti
    protected array $casts = []; 
}