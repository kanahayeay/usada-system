<?php

namespace App\Models;

/**
 * DATA CONTOH. Membaca app/Data/usada.json.
 * Saat backend siap, ganti ISI method di bawah dengan query database,
 * tapi PERTAHANKAN nama method dan bentuk data yang dikembalikan
 * (lihat docs/KONTRAK_DATA.md). Controller dan view tidak perlu diubah.
 */
class UsadaModel
{
    private array $semua = [];

    public function __construct()
    {
        $file = APPPATH . 'Data/usada.json';
        $this->semua = is_file($file) ? json_decode(file_get_contents($file), true) : [];
    }

    /** Semua entri (untuk daftar kartu). */
    public function semua(): array
    {
        return $this->semua;
    }

    public function total(): int
    {
        return count($this->semua);
    }

    /** Satu entri berdasarkan id, atau null jika tidak ada. */
    public function cari(int $id): ?array
    {
        foreach ($this->semua as $u) {
            if ($u['id'] === $id) {
                return $u;
            }
        }
        return null;
    }

    /** Satu entri berdasarkan hari pawukon ke-1..210. */
    public function berdasarkanHariKe(int $hariKe): ?array
    {
        return $this->cari($hariKe); // id = hari_ke pada data contoh
    }

    /** Pencarian teks sederhana + filter. Backend: ganti dengan WHERE/LIKE/FULLTEXT. */
    public function filter(array $f = []): array
    {
        return array_values(array_filter($this->semua, function ($u) use ($f) {
            foreach (['wuku', 'hari', 'pancawara'] as $k) {
                if (!empty($f[$k]) && strcasecmp($u[$k], $f[$k]) !== 0) {
                    return false;
                }
            }
            if (!empty($f['q'])) {
                $isi = ($u['nama_penyakit'] ?? '') . ' ' . ($u['gejala'] ?? '') . ' ' . $u['wuku'];
                if (stripos($isi, $f['q']) === false) {
                    return false;
                }
            }
            return true;
        }));
    }

    public function daftarWuku(): array
    {
        return array_values(array_unique(array_column($this->semua, 'wuku')));
    }
}