<?php

namespace App\Models;

use CodeIgniter\Model;

class EntriUsadaModel extends Model
{
    protected $table            = 'entri_usada';
    protected $primaryKey       = 'id_entri';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'id_wuku',
        'id_saptawara',
        'id_pancawara',
        'nama_penyakit',
        'gejala',
        'penyebab',
        'ritual',
        'teks_asli',
        'lembar_awal',
        'lembar_akhir',
    ];

    protected $useTimestamps = false;
}