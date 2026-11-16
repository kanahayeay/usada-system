<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GlosariumSeeder extends Seeder
{
    public function run()
    {
        $file = APPPATH . 'Database/Seeds/data/glosarium_usada.csv';

        if (!file_exists($file)) {
            throw new \RuntimeException('File CSV Glosarium tidak ditemukan.');
        }

        $handle = fopen($file, 'r');

        if ($handle === false) {
            throw new \RuntimeException('File CSV gagal dibuka.');
        }

        $header = fgetcsv($handle, 0, ',', '"', '');

        if ($header === false) {
            fclose($handle);
            throw new \RuntimeException('Header CSV tidak ditemukan.');
        }

        // Menghapus BOM jika ada
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $expectedHeader = [
            'istilah',
            'varian',
            'bahasa',
            'kategori',
            'makna_indonesia',
            'contoh_konteks',
        ];

        if ($header !== $expectedHeader) {
            fclose($handle);
            throw new \RuntimeException('Format header CSV tidak sesuai.');
        }

        $data = [];

        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }

            $item = array_combine($header, $row);

            $data[] = [
                'istilah' => trim($item['istilah']),
                'varian' => trim($item['varian']) !== ''
                    ? trim($item['varian']) : null,
                'bahasa' => trim($item['bahasa']),
                'kategori' => trim($item['kategori']),
                'makna_indonesia' => trim($item['makna_indonesia']),
                'contoh_konteks' => trim($item['contoh_konteks']) !== ''
                    ? trim($item['contoh_konteks']) : null,
            ];
        }

        fclose($handle);

        $db = \Config\Database::connect();

        if ($db->table('glosarium')->countAllResults() > 0) {
            throw new \RuntimeException(
                'Tabel glosarium sudah berisi data. Seeder dibatalkan agar tidak terjadi duplikasi.'
            );
        }

        if (!empty($data)) {
            $db->table('glosarium')->insertBatch($data);
        }

        echo count($data) . " data Glosarium berhasil dimasukkan.\n";
    }
}
