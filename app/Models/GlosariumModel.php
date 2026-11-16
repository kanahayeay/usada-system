<?php

namespace App\Models;

use CodeIgniter\Model;

class GlosariumModel extends Model
{
    protected $table            = 'glosarium';
    protected $primaryKey       = 'id_glosarium';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'istilah',
        'varian',
        'bahasa',
        'kategori',
        'makna_indonesia',
        'contoh_konteks',
    ];

    public function semua()
    {
        return $this->orderBy('istilah', 'ASC')->findAll();
    }

    public function cari($keyword = null)
    {
        if ($keyword) {
            $this->groupStart()
                ->like('istilah', $keyword)
                ->orLike('varian', $keyword)
                ->orLike('makna_indonesia', $keyword)
                ->groupEnd();
        }

        return $this->orderBy('istilah', 'ASC')->findAll();
    }

    public function kategori()
    {
        return $this->distinct()
            ->select('kategori')
            ->orderBy('kategori', 'ASC')
            ->findColumn('kategori');
    }
}
