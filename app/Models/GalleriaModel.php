<?php
namespace App\Models;
use CodeIgniter\Model;

class GalleriaModel extends Model
{
    protected $table = 'galleria';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prodotto_id', 'foto', 'foto_type'];
}