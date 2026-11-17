<?php

namespace App\Controllers;

use App\Models\GlosariumModel;

class Glosarium extends BaseController
{
    public function index()
    {
        $model = new GlosariumModel();

        // Semua istilah dikirim sekali; pencarian & filter berjalan instan di browser (public/js/glosarium.js).
        $semua = $model->semua();

        $grup        = [];   // ['A' => [istilah, ...], 'B' => [...]]
        $perKategori = [];   // ['penyakit' => 75, ...]
        $perBahasa   = [];   // ['Bali' => 150, ...]

        foreach ($semua as $row) {
            $grup[$this->hurufAwal($row['istilah'])][] = $row;
            $perKategori[$row['kategori']] = ($perKategori[$row['kategori']] ?? 0) + 1;
            $perBahasa[$row['bahasa']]     = ($perBahasa[$row['bahasa']] ?? 0) + 1;
        }

        ksort($grup);
        ksort($perKategori);
        arsort($perBahasa);

        return view('glosarium', [
            'title'       => 'Glosarium Usada Bali',
            'active'      => 'glosarium',
            'grup'        => $grup,
            'total'       => count($semua),
            'perKategori' => $perKategori,
            'perBahasa'   => $perBahasa,
            'keyword'     => (string) $this->request->getGet('keyword'),
            'kategori'    => (string) $this->request->getGet('kategori'),
            'bahasa'      => (string) $this->request->getGet('bahasa'),
        ]);
    }

    /**
     * Huruf pengelompokan A-Z. Huruf beraksen (Séngél, Tétéh) masuk ke huruf dasarnya.
     */
    private function hurufAwal(string $istilah): string
    {
        $c = mb_strtoupper(mb_substr(trim($istilah), 0, 1, 'UTF-8'), 'UTF-8');

        $c = strtr($c, [
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ē' => 'E',
            'Á' => 'A', 'À' => 'A', 'Ā' => 'A',
            'Í' => 'I', 'Ī' => 'I',
            'Ó' => 'O', 'Ō' => 'O',
            'Ú' => 'U', 'Ū' => 'U',
        ]);

        return preg_match('/^[A-Z]$/', $c) ? $c : '#';
    }
}
