<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $username = env('ADMIN_USERNAME') ?: 'adminusada';
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            throw new \RuntimeException('Variabel ADMIN_PASSWORD belum diatur.');
        }

        // Periksa apakah akun Admin sudah terdaftar.
        $sudahAda = $this->db
            ->table('users')
            ->where('username', $username)
            ->countAllResults();

        if ($sudahAda > 0) {
            echo "Akun Admin sudah tersedia. Seeder dilewati.\n";
            return;
        }

        // Buat akun Admin pertama.
        $this->db->table('users')->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nama' => 'Administrator Usada Bali',
            'status' => 'aktif',
        ]);

        echo "Akun Admin berhasil dibuat.\n";
    }
}
