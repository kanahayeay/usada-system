<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsadaModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $usadaModel = new UsadaModel();

        $stats = [
            [
                'nama' => 'Entri Usada',
                'jumlah' => $usadaModel->total(),
                'icon' => 'bi-journal-text',
            ],
            [
                'nama' => 'Data Pengobatan',
                'jumlah' => $db->table('pengobatan')->countAllResults(),
                'icon' => 'bi-capsule',
            ],
            [
                'nama' => 'Data Wuku',
                'jumlah' => $db->table('wuku')->countAllResults(),
                'icon' => 'bi-calendar-week',
            ],
            [
                'nama' => 'Akun Admin',
                'jumlah' => $db->table('users')
                    ->where('status', 'aktif')
                    ->countAllResults(),
                'icon' => 'bi-people',
            ],
        ];

        return view('admin/dashboard', [
            'title' => 'Dashboard Admin',
            'stats' => $stats,
            'namaAdmin' => session()->get('admin_nama'),
        ]);
    }
}