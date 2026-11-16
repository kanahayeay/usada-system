<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;
use Throwable;

class UsadaSeeder extends Seeder
{
    public function run()
    {
        // Pastikan seluruh tabel tujuan masih kosong.
        $tables = [
            'wuku',
            'saptawara',
            'pancawara',
            'entri_usada',
            'pengobatan',
        ];

        foreach ($tables as $table) {
            $total = $this->db->table($table)->countAllResults();

            if ($total > 0) {
                throw new RuntimeException(
                    "Tabel {$table} sudah berisi data. Seeder dibatalkan."
                );
            }
        }

        // Membaca dan memvalidasi seluruh file CSV.
        $wuku = $this->readCsv(
            'wuku_master_import.csv',
            ['id_wuku', 'nama_wuku'],
            30
        );

        $hari = $this->readCsv(
            'hari_master_import.csv',
            ['id_saptawara', 'nama_saptawara'],
            7
        );

        $pancawara = $this->readCsv(
            'pancawara_master_import.csv',
            ['id_pancawara', 'nama_pancawara'],
            5
        );

        $entri = $this->readCsv(
            'entri_usada_import.csv',
            [
                'id_entri',
                'id_wuku',
                'id_saptawara',
                'id_pancawara',
                'nama_penyakit',
                'gejala',
                'penyebab',
                'ritual',
                'teks_asli',
                'lembar_awal',
                'lembar_akhir',
            ],
            210
        );

        $pengobatan = $this->readCsv(
            'pengobatan_import.csv',
            [
                'id_pengobatan',
                'id_entri',
                'jenis',
                'bahan_asli',
                'bagian_tubuh',
                'keterangan',
                'cara',
            ],
            943
        );

        // Memasukkan data secara berurutan sesuai foreign key.
        $this->db->transBegin();

        try {
            $this->insertData('wuku', $wuku);
            $this->insertData('saptawara', $hari);
            $this->insertData('pancawara', $pancawara);
            $this->insertData('entri_usada', $entri);
            $this->insertData('pengobatan', $pengobatan);

            if ($this->db->transStatus() === false) {
                throw new RuntimeException(
                    'Terjadi kesalahan saat memasukkan data.'
                );
            }

            $this->db->transCommit();

            echo "Seeder Usada Bali berhasil dijalankan.\n";
            echo "Wuku: 30 data\n";
            echo "Saptawara: 7 data\n";
            echo "Pancawara: 5 data\n";
            echo "Entri Usada: 210 data\n";
            echo "Pengobatan: 943 data\n";
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Membaca CSV tanpa header dan memvalidasi jumlah kolom serta baris.
     */
    private function readCsv(
        string $filename,
        array $columns,
        int $expectedRows
    ): array {
        $path = __DIR__ . '/data/' . $filename;

        if (!is_file($path)) {
            throw new RuntimeException(
                "File CSV tidak ditemukan: {$filename}"
            );
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException(
                "File CSV tidak dapat dibaca: {$filename}"
            );
        }

        $data = [];

        try {
            while (
                ($row = fgetcsv($handle, 0, ',', '"', '')) !== false
            ) {
                // Lewati baris kosong.
                if ($row === [null]) {
                    continue;
                }

                // Hilangkan UTF-8 BOM jika tersedia.
                if (isset($row[0])) {
                    $row[0] = preg_replace(
                        '/^\xEF\xBB\xBF/',
                        '',
                        $row[0]
                    );
                }

                if (count($row) !== count($columns)) {
                    throw new RuntimeException(
                        "Jumlah kolom tidak sesuai pada {$filename}."
                    );
                }

                $data[] = array_combine($columns, $row);
            }
        } finally {
            fclose($handle);
        }

        if (count($data) !== $expectedRows) {
            throw new RuntimeException(
                "Jumlah data {$filename} tidak sesuai. " .
                    "Ditemukan " . count($data) .
                    ", seharusnya {$expectedRows}."
            );
        }

        return $data;
    }

    /**
     * Memasukkan data ke tabel dalam batch.
     */
    private function insertData(string $table, array $data): void
    {
        foreach (array_chunk($data, 100) as $batch) {
            if (
                $this->db->table($table)->insertBatch($batch) === false
            ) {
                throw new RuntimeException(
                    "Gagal memasukkan data ke tabel {$table}."
                );
            }
        }
    }
}
