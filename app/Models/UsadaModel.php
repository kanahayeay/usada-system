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


    /**
     * Query dasar statistik dengan relasi kalender dan filter aktif.
     * Filter hanya menggunakan nama yang berasal dari parameter GET.
     */
    private function queryStatistik(array $filter = [])
    {
        $builder = $this->db->table('entri_usada e')
            ->join('wuku w', 'w.id_wuku = e.id_wuku')
            ->join('saptawara s', 's.id_saptawara = e.id_saptawara')
            ->join('pancawara p', 'p.id_pancawara = e.id_pancawara');

        if (!empty($filter['wuku'])) {
            $builder->where('w.nama_wuku', $filter['wuku']);
        }

        if (!empty($filter['saptawara'])) {
            $builder->where('s.nama_saptawara', $filter['saptawara']);
        }

        if (!empty($filter['pancawara'])) {
            $builder->where('p.nama_pancawara', $filter['pancawara']);
        }

        return $builder;
    }

    /**
     * Ringkasan metrik utama untuk seluruh entri yang lolos filter.
     * Penghitungan dilakukan di database menggunakan COUNT DISTINCT.
     */
    public function statistikRingkasan(array $filter = []): array
    {
        $row = $this->queryStatistik($filter)
            ->select([
                'COUNT(DISTINCT e.id_entri) AS total_entri',
                'COUNT(DISTINCT e.id_wuku) AS total_wuku',
                'COUNT(DISTINCT e.id_saptawara) AS total_saptawara',
                'COUNT(DISTINCT e.id_pancawara) AS total_pancawara',
                'COUNT(DISTINCT pg.id_pengobatan) AS total_pengobatan',
            ])
            ->join('pengobatan pg', 'pg.id_entri = e.id_entri', 'left')
            ->get()
            ->getRowArray();

        return [
            'total_entri'       => (int) ($row['total_entri'] ?? 0),
            'total_wuku'        => (int) ($row['total_wuku'] ?? 0),
            'total_saptawara'   => (int) ($row['total_saptawara'] ?? 0),
            'total_pancawara'   => (int) ($row['total_pancawara'] ?? 0),
            'total_pengobatan'  => (int) ($row['total_pengobatan'] ?? 0),
        ];
    }

    /**
     * Distribusi jumlah catatan pengobatan berdasarkan dimensi kalender.
     * LEFT JOIN menjaga agar kategori kalender tetap muncul meskipun jumlahnya 0.
     */
    public function distribusiPengobatan(string $dimensi, array $filter = []): array
    {
        $kolom = [
            'wuku' => [
                'id'   => 'e.id_wuku',
                'nama' => 'w.nama_wuku',
            ],
            'saptawara' => [
                'id'   => 'e.id_saptawara',
                'nama' => 's.nama_saptawara',
            ],
            'pancawara' => [
                'id'   => 'e.id_pancawara',
                'nama' => 'p.nama_pancawara',
            ],
        ];

        if (!isset($kolom[$dimensi])) {
            return [];
        }

        $idKolom = $kolom[$dimensi]['id'];
        $namaKolom = $kolom[$dimensi]['nama'];

        return $this->queryStatistik($filter)
            ->select("{$idKolom} AS id_dimensi, {$namaKolom} AS nama_dimensi, COUNT(pg.id_pengobatan) AS jumlah_pengobatan", false)
            ->join('pengobatan pg', 'pg.id_entri = e.id_entri', 'left')
            ->groupBy([$idKolom, $namaKolom])
            ->orderBy($idKolom, 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Distribusi catatan pengobatan berdasarkan jenis pengobatan.
     * Penanda urutan seperti [1], [2], dan [3] di awal nama diabaikan
     * agar nama jenis yang sama dihitung sebagai satu kategori.
     */
    public function distribusiJenisPengobatan(array $filter = []): array
    {
        $rows = $this->queryStatistik($filter)
            ->select('pg.jenis')
            ->join('pengobatan pg', 'pg.id_entri = e.id_entri')
            ->where("TRIM(pg.jenis) <> ''", null, false)
            ->where("TRIM(pg.jenis) <> '-'", null, false)
            ->get()
            ->getResultArray();

        $kelompok = [];

        foreach ($rows as $row) {
            $jenis = trim((string) ($row['jenis'] ?? ''));

            // Hapus penanda angka di awal, misalnya "[1] jamu" menjadi "jamu".
            $jenis = preg_replace('/^\[\d+\]\s*/u', '', $jenis) ?? $jenis;
            $jenis = trim($jenis);

            if ($jenis === '' || $jenis === '-') {
                continue;
            }

            // Normalisasi kunci agar perbedaan kapitalisasi tidak membuat kategori baru.
            $kunci = function_exists('mb_strtolower')
                ? mb_strtolower($jenis, 'UTF-8')
                : strtolower($jenis);

            if (!isset($kelompok[$kunci])) {
                $kelompok[$kunci] = [
                    'jenis' => $jenis,
                    'jumlah' => 0,
                ];
            }

            $kelompok[$kunci]['jumlah']++;
        }

        $hasil = array_values($kelompok);

        usort($hasil, static function (array $a, array $b): int {
            $urutJumlah = $b['jumlah'] <=> $a['jumlah'];
            return $urutJumlah !== 0
                ? $urutJumlah
                : strcasecmp($a['jenis'], $b['jenis']);
        });

        return $hasil;
    }

    /**
     * Menghitung kelengkapan atribut entri dan keberadaan pengobatan.
     * Nilai NULL, string kosong, dan tanda '-' dianggap belum terisi.
     */
    public function statistikKelengkapan(array $filter = []): array
    {
        $select = "
            COUNT(e.id_entri) AS total_entri,
            SUM(CASE WHEN NULLIF(TRIM(e.nama_penyakit), '') IS NOT NULL AND TRIM(e.nama_penyakit) <> '-' THEN 1 ELSE 0 END) AS nama_penyakit_lengkap,
            SUM(CASE WHEN NULLIF(TRIM(e.penyebab), '') IS NOT NULL AND TRIM(e.penyebab) <> '-' THEN 1 ELSE 0 END) AS penyebab_lengkap,
            SUM(CASE WHEN NULLIF(TRIM(e.ritual), '') IS NOT NULL AND TRIM(e.ritual) <> '-' THEN 1 ELSE 0 END) AS ritual_lengkap,
            SUM(CASE WHEN EXISTS (SELECT 1 FROM pengobatan pg WHERE pg.id_entri = e.id_entri) THEN 1 ELSE 0 END) AS pengobatan_lengkap
        ";

        $row = $this->queryStatistik($filter)
            ->select($select, false)
            ->get()
            ->getRowArray();

        $total = (int) ($row['total_entri'] ?? 0);
        $hasil = ['total_entri' => $total];

        foreach (['nama_penyakit', 'penyebab', 'ritual', 'pengobatan'] as $field) {
            $lengkap = (int) ($row[$field . '_lengkap'] ?? 0);
            $hasil[$field] = [
                'lengkap' => $lengkap,
                'kosong'  => max(0, $total - $lengkap),
            ];
        }

        return $hasil;
    }

    /**
     * Opsi filter kalender untuk halaman Statistik.
     */
    public function opsiFilterStatistik(): array
    {
        return [
            'wuku' => $this->db->table('wuku')
                ->select('nama_wuku')
                ->orderBy('id_wuku', 'ASC')
                ->get()
                ->getResultArray(),
            'saptawara' => $this->db->table('saptawara')
                ->select('nama_saptawara')
                ->orderBy('id_saptawara', 'ASC')
                ->get()
                ->getResultArray(),
            'pancawara' => $this->db->table('pancawara')
                ->select('nama_pancawara')
                ->orderBy('id_pancawara', 'ASC')
                ->get()
                ->getResultArray(),
        ];
    }

}