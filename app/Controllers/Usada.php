<?php

namespace App\Controllers;

use App\Models\UsadaModel;

class Usada extends BaseController
{
    private const PER_HALAMAN = 9;
    private const PER_HALAMAN_BAHAN = 10;

    public function index(): string
    {
        helper('usada');

        $model = new UsadaModel();

        // Mengambil parameter filter dari URL.
        $filter = [
            'wuku'      => trim((string) $this->request->getGet('wuku')),
            'hari'      => trim((string) $this->request->getGet('hari')),
            'pancawara' => trim((string) $this->request->getGet('pancawara')),
            'q'         => trim((string) $this->request->getGet('q')),
        ];

        // Mengambil data berdasarkan filter.
        $hasil = $model->filter($filter);

        // Menghitung jumlah halaman.
        $jumlahHalaman = max(
            1,
            (int) ceil(count($hasil) / self::PER_HALAMAN)
        );

        // Memastikan nomor halaman berada dalam rentang yang valid.
        $halaman = min(
            max(1, (int) $this->request->getGet('hal')),
            $jumlahHalaman
        );

        // Mengambil data sesuai halaman aktif.
        $daftar = array_slice(
            $hasil,
            ($halaman - 1) * self::PER_HALAMAN,
            self::PER_HALAMAN
        );

        // Mengambil ID detail dari URL.
        $id = (int) $this->request->getGet('id');

        // Mengambil detail langsung berdasarkan ID dari database.
        $terpilih = $id > 0
            ? $model->cari($id)
            : null;

        // Menentukan posisi entri terpilih pada daftar halaman aktif.
        $indeksTerpilih = null;

        if ($terpilih !== null) {
            foreach ($daftar as $indeks => $entri) {
                if ((int) $entri['id'] === (int) $terpilih['id']) {
                    $indeksTerpilih = $indeks;
                    break;
                }
            }
        }

        // Navigasi mengikuti urutan hasil filter pada halaman aktif.
        $sebelumnya = (
            $indeksTerpilih !== null &&
            $indeksTerpilih > 0
        )
            ? $daftar[$indeksTerpilih - 1]
            : null;

        $berikutnya = (
            $indeksTerpilih !== null &&
            isset($daftar[$indeksTerpilih + 1])
        )
            ? $daftar[$indeksTerpilih + 1]
            : null;

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
                'hari'      => [
                    'Minggu',
                    'Senin',
                    'Selasa',
                    'Rabu',
                    'Kamis',
                    'Jumat',
                    'Sabtu',
                ],
                'pancawara' => [
                    'Umanis',
                    'Pahing',
                    'Pwon',
                    'Wage',
                    'Kliwon',
                ],
            ],

            'filter'        => $filter,
            'totalSemua'    => $model->total(),
            'totalHasil'    => count($hasil),

            'daftar'        => $daftar,
            'halaman'       => $halaman,
            'jumlahHalaman' => $jumlahHalaman,

            'terpilih'      => $terpilih,
            'sebelumnya'    => $sebelumnya,
            'berikutnya'    => $berikutnya,
        ]);
    }

    public function cariBahan(): string
    {
        helper('usada');

        $model = new UsadaModel();

        $keyword = trim((string) $this->request->getGet('q'));

        $hasil = $keyword !== ''
            ? $model->cariBahan($keyword)
            : [];

        // Pagination Cari Bahan.
        $jumlahHalaman = max(
            1,
            (int) ceil(count($hasil) / self::PER_HALAMAN_BAHAN)
        );

        $halaman = min(
            max(1, (int) $this->request->getGet('hal')),
            $jumlahHalaman
        );

        $daftar = array_slice(
            $hasil,
            ($halaman - 1) * self::PER_HALAMAN_BAHAN,
            self::PER_HALAMAN_BAHAN
        );

        return view('cari_bahan', [
            'title'         => 'Cari Bahan Herbal',
            'active'        => 'bahan',
            'keyword'       => $keyword,

            // Data hasil yang ditampilkan.
            'hasil'         => $daftar,
            'total'         => count($hasil),

            // Data pagination.
            'halaman'       => $halaman,
            'jumlahHalaman' => $jumlahHalaman,
        ]);
    }
}
