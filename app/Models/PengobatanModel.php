<?php

namespace App\Models;

use CodeIgniter\Model;

class PengobatanModel extends Model
{
    protected $table            = 'pengobatan';
    protected $primaryKey       = 'id_pengobatan';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'id_entri',
        'jenis',
        'bahan_asli',
        'bagian_tubuh',
        'keterangan',
        'cara',
    ];

    protected $useTimestamps = false;
}