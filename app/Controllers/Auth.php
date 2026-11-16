<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    /**
     * Menampilkan halaman login Admin.
     */
    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(site_url('admin'));
        }

        return view('admin/auth/login', [
            'title' => 'Login Admin',
        ]);
    }

    /**
     * Memproses autentikasi Admin.
     */
    public function attempt()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        // Validasi input.
        if ($username === '' || $password === '') {
            return redirect()
                ->to(site_url('admin/login'))
                ->withInput()
                ->with('error', 'Username dan password wajib diisi.');
        }

        $userModel = new UserModel();

        // Mencari akun berdasarkan username.
        $user = $userModel
            ->where('username', $username)
            ->first();

        // Memeriksa akun, status, dan password.
        if (
            !$user ||
            $user['status'] !== 'aktif' ||
            !password_verify($password, $user['password'])
        ) {
            return redirect()
                ->to(site_url('admin/login'))
                ->withInput()
                ->with('error', 'Username atau password tidak valid.');
        }

        // Memperbarui session ID setelah login berhasil.
        session()->regenerate(true);

        // Menyimpan informasi Admin ke session.
        session()->set([
            'admin_logged_in' => true,
            'admin_id'        => $user['id_user'],
            'admin_username'  => $user['username'],
            'admin_nama'      => $user['nama'],
        ]);

        return redirect()->to(site_url('admin'));
    }

    /**
     * Mengakhiri sesi login Admin.
     */
    public function logout()
    {
        session()->destroy();

        return redirect()
            ->to(site_url('admin/login'))
            ->with('success', 'Kamu berhasil logout.');
    }
}