<?php

namespace App\Models;

use CodeIgniter\Model;

class UsadaModel extends Model
{
    protected $table = 'entri_usada';
    protected $primaryKey = 'id_entri';
    protected $returnType = 'array';

    /**
     * Mengubah nilai kosong atau tanda "-" menjadi null.
     */
    private function nilaiAtauNull($nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        $nilai = trim((string) $nilai);

        return ($nilai === '' || $nilai === '-') ? null : $nilai;
    }

    /**
     * Mengambil data entri beserta informasi kalender.
     */
    private function queryEntri()
    {
        return $this->db->table('entri_usada e')
            ->select([
                'e.id_entri',
                'e.id_wuku',
                'e.id_saptawara',
                'e.id_pancawara',
                'e.nama_penyakit',
                'e.gejala',
                'e.penyebab',
                'e.ritual',
                'e.teks_asli',
                'e.lembar_awal',
                'e.lembar_akhir',
                'w.nama_wuku',
                's.nama_saptawara',
                'p.nama_pancawara',
            ])
            ->join('wuku w', 'w.id_wuku = e.id_wuku')
            ->join('saptawara s', 's.id_saptawara = e.id_saptawara')
            ->join('pancawara p', 'p.id_pancawara = e.id_pancawara');
    }

    /**
     * Membentuk array sesuai kontrak data frontend.
     */
    private function bentukData(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        // Ambil seluruh pengobatan untuk entri yang ditemukan.
        $ids = array_column($rows, 'id_entri');

        $daftarPengobatan = $this->db
            ->table('pengobatan')
            ->whereIn('id_entri', $ids)
            ->orderBy('id_pengobatan', 'ASC')
            ->get()
            ->getResultArray();

        // Kelompokkan pengobatan berdasarkan id_entri.
        $pengobatanPerEntri = [];

        foreach ($daftarPengobatan as $p) {
            $pengobatanPerEntri[$p['id_entri']][] = [
                'jenis'        => $this->nilaiAtauNull($p['jenis']),
                'bahan'        => $this->nilaiAtauNull($p['bahan_asli']),
                'bagian_tubuh' => $this->nilaiAtauNull($p['bagian_tubuh']),
                'keterangan'   => $this->nilaiAtauNull($p['keterangan']),
                'cara'         => $this->nilaiAtauNull($p['cara']),
            ];
        }

        $hasil = [];

        foreach ($rows as $row) {
            $id = (int) $row['id_entri'];

            $hasil[] = [
                'id'            => $id,
                'hari_ke'       => $id,
                'wuku'          => $row['nama_wuku'],
                'wuku_no'       => (int) $row['id_wuku'],
                'hari'          => $row['nama_saptawara'],
                'pancawara'     => $row['nama_pancawara'],
                'nama_penyakit' => $this->nilaiAtauNull($row['nama_penyakit']),
                'gejala'        => $this->nilaiAtauNull($row['gejala']),
                'penyebab'      => $this->nilaiAtauNull($row['penyebab']),
                'ritual'        => $this->nilaiAtauNull($row['ritual']),
                'teks_asli'     => $row['teks_asli'] ?? '',
                'lembar_awal'   => $this->nilaiAtauNull($row['lembar_awal']),
                'lembar_akhir'  => $this->nilaiAtauNull($row['lembar_akhir']),
                'pengobatan'    => $pengobatanPerEntri[$id] ?? [],
            ];
        }

        return $hasil;
    }

    /**
     * Semua entri Usada.
     */
    public function semua(): array
    {
        $rows = $this->queryEntri()
            ->orderBy('e.id_entri', 'ASC')
            ->get()
            ->getResultArray();

        return $this->bentukData($rows);
    }

    /**
     * Jumlah seluruh entri.
     */
    public function total(): int
    {
        return $this->db->table('entri_usada')->countAllResults();
    }

    /**
     * Satu entri berdasarkan ID.
     */
    public function cari(int $id): ?array
    {
        $rows = $this->queryEntri()
            ->where('e.id_entri', $id)
            ->limit(1)
            ->get()
            ->getResultArray();

        $hasil = $this->bentukData($rows);

        return $hasil[0] ?? null;
    }

    /**
     * Satu entri berdasarkan hari Pawukon 1-210.
     */
    public function berdasarkanHariKe(int $hariKe): ?array
    {
        if ($hariKe < 1 || $hariKe > 210) {
            return null;
        }

        return $this->cari($hariKe);
    }

    /**
     * Filter berdasarkan Wuku, Saptawara, Pancawara, dan kata kunci.
     */
    public function filter(array $f = []): array
    {
        $builder = $this->queryEntri();

        if (!empty($f['wuku'])) {
            $builder->where('w.nama_wuku', $f['wuku']);
        }

        if (!empty($f['hari'])) {
            $builder->where('s.nama_saptawara', $f['hari']);
        }

        if (!empty($f['pancawara'])) {
            $builder->where('p.nama_pancawara', $f['pancawara']);
        }

        if (!empty($f['q'])) {
            $builder->groupStart()
                ->like('e.nama_penyakit', $f['q'])
                ->orLike('e.gejala', $f['q'])
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('e.id_entri', 'ASC')
            ->get()
            ->getResultArray();

        return $this->bentukData($rows);
    }

    /**
     * Daftar 30 nama Wuku berdasarkan urutannya.
     */
    public function daftarWuku(): array
    {
        $rows = $this->db->table('wuku')
            ->select('nama_wuku')
            ->orderBy('id_wuku', 'ASC')
            ->get()
            ->getResultArray();

        return array_column($rows, 'nama_wuku');
    }
}
