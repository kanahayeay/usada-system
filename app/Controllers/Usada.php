<?php

namespace App\Controllers;

use App\Models\UsadaModel;

class Usada extends BaseController
{
    private const PER_HALAMAN = 9;

    public function index(): string
    {
        helper('usada');
        $model = new UsadaModel();

        $filter = [
            'wuku'      => trim((string) $this->request->getGet('wuku')),
            'hari'      => trim((string) $this->request->getGet('hari')),
            'pancawara' => trim((string) $this->request->getGet('pancawara')),
            'q'         => trim((string) $this->request->getGet('q')),
        ];

        $hasil         = $model->filter($filter);
        $jumlahHalaman = max(1, (int) ceil(count($hasil) / self::PER_HALAMAN));
        $halaman       = min(max(1, (int) $this->request->getGet('hal')), $jumlahHalaman);
        $daftar        = array_slice($hasil, ($halaman - 1) * self::PER_HALAMAN, self::PER_HALAMAN);

        // Entri yang detailnya ditampilkan: ?id=..., atau entri pertama pada halaman ini.
        $id       = (int) $this->request->getGet('id');
        $terpilih = ($id ? $model->cari($id) : null) ?? ($daftar[0] ?? null);

        return view('struktur', [
            'title'         => 'Struktur & Detail Usada',
            'active'        => 'struktur',
            'dimensi'       => [
                ['bi-calendar3', 'Wuku', 'Siklus 30'],
                ['bi-brightness-high', 'Saptawara', '7 Hari'],
                ['bi-compass', 'Pancawara', '5 Pasaran'],
                ['bi-journal-text', 'Entri Usada', 'Diagnosa'],
                ['bi-capsule', 'Pengobatan', 'Bahan & Cara'],
                ['bi-flower1', 'Ritual', 'Sesaji & Mantra'],
                ['bi-book', 'Teks Asli', 'Naskah Lontar'],
            ],
            'dimensiAktif'  => 3,
            'opsi'          => [
                'wuku'      => $model->daftarWuku(),
                'hari'      => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                'pancawara' => ['Umanis', 'Pahing', 'Pwon', 'Wage', 'Kliwon'],
            ],
            'filter'        => $filter,
            'totalSemua'    => $model->total(),
            'totalHasil'    => count($hasil),
            'daftar'        => $daftar,
            'halaman'       => $halaman,
            'jumlahHalaman' => $jumlahHalaman,
            'terpilih'      => $terpilih,
            'sebelumnya'    => $terpilih ? $model->cari($terpilih['id'] - 1) : null,
            'berikutnya'    => $terpilih ? $model->cari($terpilih['id'] + 1) : null,
        ]);
    }
}