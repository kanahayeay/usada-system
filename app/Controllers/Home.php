<?php

namespace App\Controllers;

use App\Libraries\Pawukon;
use App\Models\UsadaModel;

class Home extends BaseController
{
    public function index(): string
    {
        helper('usada');
        $model = new UsadaModel();

        // Tanggal dari form (?tanggal=YYYY-MM-DD); jika tidak valid, pakai hari ini (WITA).
        $zona     = new \DateTimeZone('Asia/Makassar');
        $hariIni  = (new \DateTimeImmutable('now', $zona))->format('Y-m-d');
        $tanggal  = (string) $this->request->getGet('tanggal');
        $parsed   = \DateTimeImmutable::createFromFormat('!Y-m-d', $tanggal, $zona);
        if (!$parsed || $parsed->format('Y-m-d') !== $tanggal) {
            $tanggal = $hariIni;
        }

        return view('beranda', [
            'title'         => 'Beranda',
            'active'        => 'beranda',
            'stats'         => [
                ['angka' => $model->total(), 'nama' => 'Entri Lontar'],
                ['angka' => 30, 'nama' => 'Wuku'],
                ['angka' => 7,  'nama' => 'Saptawara'],
                ['angka' => 5,  'nama' => 'Pancawara'],
            ],
            'tanggal'       => $tanggal,
            'adalahHariIni' => $tanggal === $hariIni,
            'usada'         => $model->berdasarkanHariKe(Pawukon::hariKe($tanggal)),
        ]);
    }
}