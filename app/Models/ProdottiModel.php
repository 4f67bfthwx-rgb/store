<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdottiModel extends Model
{
    protected $table            = 'prodotti';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    protected $allowedFields    = [
        'nome', 
        'descrizione', 
        'prezzo', 
        'vestibilita', 
        'magazzino', 
        'immagine', 
        'immagine_type'
    ];

    // GESTIONE DATE AUTOMATICA
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // FIX ERRORE: Definiamo esplicitamente che è un array vuoto
    protected array $casts = [];
}