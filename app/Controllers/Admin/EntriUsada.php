<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EntriUsadaModel;

class EntriUsada extends BaseController
{
    private function model(): EntriUsadaModel
    {
        return new EntriUsadaModel();
    }

    private function dataAdmin(array $data = []): array
    {
        return array_merge([
            'namaAdmin' => session()->get('admin_nama') ?? 'Admin',
            'active'    => 'entri_usada',
        ], $data);
    }

    /**
     * Mengambil data referensi untuk dropdown.
     */
    private function dataReferensi(): array
    {
        $db = \Config\Database::connect();

        return [
            'daftarWuku' => $db->table('wuku')
                ->orderBy('nama_wuku', 'ASC')
                ->get()
                ->getResultArray(),

            'daftarSaptawara' => $db->table('saptawara')
                ->orderBy('nama_saptawara', 'ASC')
                ->get()
                ->getResultArray(),

            'daftarPancawara' => $db->table('pancawara')
                ->orderBy('nama_pancawara', 'ASC')
                ->get()
                ->getResultArray(),
        ];
    }

    /**
     * Menampilkan daftar Data Usada.
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));

        $model = $this->model();

        if ($keyword !== '') {
            $model->groupStart()
                ->like('nama_penyakit', $keyword)
                ->orLike('gejala', $keyword)
                ->orLike('penyebab', $keyword)
                ->orLike('ritual', $keyword)
                ->groupEnd();
        }

        $data = $this->dataAdmin(array_merge([
            'title'      => 'Kelola Data Usada',
            'entriUsada' => $model
                ->orderBy('id_entri', 'ASC')
                ->paginate(10),
            'pager'      => $model->pager,
            'keyword'    => $keyword,
        ], $this->dataReferensi()));

        return view('admin/entri_usada/index', $data);
    }

    /**
     * Validasi form Data Usada.
     */
    private function aturanValidasi(): array
    {
        return [
            'nama_penyakit' => [
                'label' => 'Nama Penyakit',
                'rules' => 'required|max_length[255]',
            ],
            'id_wuku' => [
                'label' => 'Wuku',
                'rules' => 'required|is_not_unique[wuku.id_wuku]',
            ],
            'id_saptawara' => [
                'label' => 'Saptawara',
                'rules' => 'required|is_not_unique[saptawara.id_saptawara]',
            ],
            'id_pancawara' => [
                'label' => 'Pancawara',
                'rules' => 'required|is_not_unique[pancawara.id_pancawara]',
            ],
            'gejala' => [
                'label' => 'Gejala',
                'rules' => 'required',
            ],
            'penyebab' => [
                'label' => 'Penyebab',
                'rules' => 'required',
            ],
            'ritual' => [
                'label' => 'Ritual',
                'rules' => 'required',
            ],
            'teks_asli' => [
                'label' => 'Teks Asli',
                'rules' => 'required',
            ],
            'lembar_awal' => [
                'label' => 'Lembar Awal',
                'rules' => 'required|max_length[10]',
            ],
            'lembar_akhir' => [
                'label' => 'Lembar Akhir',
                'rules' => 'required|max_length[10]',
            ],
        ];
    }

    /**
     * Mengambil data dari form.
     */
    private function dataForm(): array
    {
        return [
            'nama_penyakit' => trim((string) $this->request->getPost('nama_penyakit')),
            'id_wuku'       => $this->request->getPost('id_wuku'),
            'id_saptawara'  => $this->request->getPost('id_saptawara'),
            'id_pancawara'  => $this->request->getPost('id_pancawara'),
            'gejala'        => trim((string) $this->request->getPost('gejala')),
            'penyebab'      => trim((string) $this->request->getPost('penyebab')),
            'ritual'        => trim((string) $this->request->getPost('ritual')),
            'teks_asli'     => trim((string) $this->request->getPost('teks_asli')),
            'lembar_awal'   => trim((string) $this->request->getPost('lembar_awal')),
            'lembar_akhir'  => trim((string) $this->request->getPost('lembar_akhir')),
        ];
    }

    /**
     * Menambahkan entri Usada.
     */
    public function simpan()
    {
        $model = $this->model();

        if (!$this->validate($this->aturanValidasi())) {
            return redirect()->to(site_url('admin/entri-usada'))
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Data belum berhasil disimpan. Periksa kembali isian form.');
        }

        if (!$model->insert($this->dataForm())) {
            return redirect()->to(site_url('admin/entri-usada'))
                ->withInput()
                ->with('error', 'Data Usada gagal ditambahkan.');
        }

        return redirect()->to(site_url('admin/entri-usada'))
            ->with('success', 'Data Usada berhasil ditambahkan.');
    }

    /**
     * Memperbarui entri Usada.
     */
    public function update($id)
    {
        $model = $this->model();

        if (!$model->find($id)) {
            return redirect()->to(site_url('admin/entri-usada'))
                ->with('error', 'Data Usada tidak ditemukan.');
        }

        if (!$this->validate($this->aturanValidasi())) {
            return redirect()->to(site_url('admin/entri-usada'))
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Data belum berhasil diperbarui. Periksa kembali isian form.');
        }

        if (!$model->update($id, $this->dataForm())) {
            return redirect()->to(site_url('admin/entri-usada'))
                ->with('error', 'Data Usada gagal diperbarui.');
        }

        return redirect()->to(site_url('admin/entri-usada'))
            ->with('success', 'Data Usada berhasil diperbarui.');
    }

    /**
     * Menghapus entri Usada.
     */
    public function hapus($id)
    {
        $model = $this->model();

        if (!$model->find($id)) {
            return redirect()->to(site_url('admin/entri-usada'))
                ->with('error', 'Data Usada tidak ditemukan.');
        }

        if (!$model->delete($id)) {
            return redirect()->to(site_url('admin/entri-usada'))
                ->with('error', 'Data Usada gagal dihapus.');
        }

        return redirect()->to(site_url('admin/entri-usada'))
            ->with('success', 'Data Usada berhasil dihapus.');
    }
}