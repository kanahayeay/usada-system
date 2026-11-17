<?php

namespace App\Controllers;

use App\Models\UsadaModel;

class Statistik extends BaseController
{
    /**
     * Menampilkan halaman Statistik dengan agregasi terfilter dan temuan global.
     */
    public function index()
    {
        $model = new UsadaModel();

        $filter = [
            'wuku'      => trim((string) $this->request->getGet('wuku')),
            'saptawara' => trim((string) $this->request->getGet('saptawara')),
            'pancawara' => trim((string) $this->request->getGet('pancawara')),
        ];

        $data = [
            'title' => 'Statistik',
            'active' => 'statistik',
            'filter' => $filter,
            'opsi' => $model->opsiFilterStatistik(),
            'ringkasan' => $model->statistikRingkasan($filter),
            'distribusi_pengobatan' => [
                'wuku'      => $model->distribusiPengobatan('wuku', $filter),
                'saptawara' => $model->distribusiPengobatan('saptawara', $filter),
                'pancawara' => $model->distribusiPengobatan('pancawara', $filter),
            ],
            'jenis_pengobatan' => $model->distribusiJenisPengobatan($filter),
            'kelengkapan' => $model->statistikKelengkapan($filter),
            // Temuan statistik bersifat global dan tidak mengikuti filter GET.
            'temuan_statistik' => [
                'distribusi_pengobatan' => [
                    'wuku'      => $model->distribusiPengobatan('wuku'),
                    'saptawara' => $model->distribusiPengobatan('saptawara'),
                    'pancawara' => $model->distribusiPengobatan('pancawara'),
                ],
                'jenis_pengobatan' => $model->distribusiJenisPengobatan(),
            ],
        ];

        return view('statistik', $data);
    }
}