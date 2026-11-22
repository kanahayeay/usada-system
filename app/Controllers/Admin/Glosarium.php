<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GlosariumModel;

class Glosarium extends BaseController
{
    /**
     * Mengambil Model Glosarium.
     */
    private function model(): GlosariumModel
    {
        return new GlosariumModel();
    }

    /**
     * Data umum untuk halaman Admin.
     */
    private function dataAdmin(array $data = []): array
    {
        return array_merge([
            'namaAdmin' => session()->get('admin_nama') ?? 'Admin',
            'active'    => 'glosarium',
        ], $data);
    }

    /**
     * Menampilkan daftar Glosarium.
     */
    public function index()
    {
        $keyword  = trim((string) $this->request->getGet('keyword'));
        $kategori = trim((string) $this->request->getGet('kategori'));
        $bahasa   = trim((string) $this->request->getGet('bahasa'));

        $model = $this->model();

        // Pencarian istilah, varian, dan makna.
        if ($keyword !== '') {
            $model->groupStart()
                ->like('istilah', $keyword)
                ->orLike('varian', $keyword)
                ->orLike('makna_indonesia', $keyword)
                ->groupEnd();
        }

        // Filter kategori.
        if ($kategori !== '') {
            $model->where('kategori', $kategori);
        }

        // Filter bahasa.
        if ($bahasa !== '') {
            $model->where('bahasa', $bahasa);
        }

        $data = $this->dataAdmin([
            'title'          => 'Kelola Glosarium',
            'glosarium'      => $model->orderBy('istilah', 'ASC')->paginate(10),
            'pager'          => $model->pager,
            'kategori'       => $this->model()->kategori(),
            'keyword'        => $keyword,
            'filterKategori' => $kategori,
            'filterBahasa'   => $bahasa,
        ]);

        return view('admin/glosarium/index', $data);
    }

    /**
     * Menampilkan formulir tambah Glosarium.
     */
    public function tambah()
    {
        $data = $this->dataAdmin([
            'title' => 'Tambah Istilah Glosarium',
            'mode'  => 'tambah',
            'item'  => [
                'istilah'         => '',
                'varian'          => '',
                'bahasa'          => '',
                'kategori'        => '',
                'makna_indonesia' => '',
                'contoh_konteks'  => '',
            ],
        ]);

        return view('admin/glosarium/form', $data);
    }

    /**
     * Validasi data Glosarium.
     */
    private function aturanValidasi(): array
    {
        return [
            'istilah' => [
                'rules'  => 'required|max_length[150]',
                'errors' => [
                    'required' => 'Istilah wajib diisi.',
                    'max_length' => 'Istilah maksimal 150 karakter.',
                ],
            ],

            'varian' => 'permit_empty|max_length[150]',

            'bahasa' => [
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Bahasa wajib diisi.',
                    'max_length' => 'Bahasa maksimal 50 karakter.',
                ],
            ],

            'kategori' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Kategori wajib diisi.',
                    'max_length' => 'Kategori maksimal 100 karakter.',
                ],
            ],

            'makna_indonesia' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Makna dalam Bahasa Indonesia wajib diisi.',
                ],
            ],

            'contoh_konteks' => 'permit_empty',
        ];
    }

    /**
     * Mengambil dan membersihkan data dari formulir.
     */
    private function dataForm(): array
    {
        return [
            'istilah' => trim((string) $this->request->getPost('istilah')),

            'varian' => trim((string) $this->request->getPost('varian')) ?: null,

            'bahasa' => trim((string) $this->request->getPost('bahasa')),

            'kategori' => trim((string) $this->request->getPost('kategori')),

            'makna_indonesia' => trim((string) $this->request->getPost('makna_indonesia')),

            'contoh_konteks' => trim((string) $this->request->getPost('contoh_konteks')) ?: null,
        ];
    }

    /**
     * Menyimpan istilah baru.
     */
    public function simpan()
    {
        // Validasi input.
        if (!$this->validate($this->aturanValidasi())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Simpan ke database.
        $inserted = $this->model()->insert($this->dataForm());

        if ($inserted === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Istilah gagal ditambahkan. Silakan coba lagi.');
        }

        return redirect()->to(site_url('admin/glosarium'))
            ->with('success', 'Istilah Glosarium berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir edit.
     */
    public function edit($id = null)
    {
        $item = $this->model()->find($id);

        if (!$item) {
            return redirect()->to(site_url('admin/glosarium'))
                ->with('error', 'Data Glosarium tidak ditemukan.');
        }

        $data = $this->dataAdmin([
            'title' => 'Edit Istilah Glosarium',
            'mode'  => 'edit',
            'item'  => $item,
        ]);

        return view('admin/glosarium/form', $data);
    }

    /**
     * Memperbarui istilah.
     */
    public function update($id = null)
    {
        $model = $this->model();

        // Pastikan data tersedia.
        if (!$model->find($id)) {
            return redirect()->to(site_url('admin/glosarium'))
                ->with('error', 'Data Glosarium tidak ditemukan.');
        }

        // Validasi input.
        if (!$this->validate($this->aturanValidasi())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Perbarui data.
        $updated = $model->update($id, $this->dataForm());

        if ($updated === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Perubahan istilah gagal disimpan.');
        }

        return redirect()->to(site_url('admin/glosarium'))
            ->with('success', 'Perubahan istilah berhasil disimpan!');
    }

    /**
     * Menghapus istilah.
     */
    public function hapus($id = null)
    {
        $model = $this->model();

        // Pastikan data tersedia.
        if (!$model->find($id)) {
            return redirect()->to(site_url('admin/glosarium'))
                ->with('error', 'Data Glosarium tidak ditemukan.');
        }

        // Hapus data dari database.
        $deleted = $model->delete($id);

        if ($deleted === false) {
            return redirect()->to(site_url('admin/glosarium'))
                ->with('error', 'Istilah gagal dihapus. Silakan coba lagi.');
        }

        return redirect()->to(site_url('admin/glosarium'))
            ->with('success', 'Istilah Glosarium berhasil dihapus!');
    }
}