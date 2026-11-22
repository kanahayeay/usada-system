<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengobatanModel;
use App\Models\EntriUsadaModel;

class Pengobatan extends BaseController
{
    protected $pengobatanModel;
    protected $entriUsadaModel;

    public function __construct()
    {
        $this->pengobatanModel = new PengobatanModel();
        $this->entriUsadaModel = new EntriUsadaModel();
    }

    // =========================================
    // MENAMPILKAN HALAMAN PENGOBATAN
    // =========================================

    public function index()
    {
        $data = [
            'title' => 'Kelola Pengobatan',
            'active' => 'pengobatan',

            'pengobatan' => $this->pengobatanModel
                ->select('pengobatan.*, entri_usada.nama_penyakit')
                ->join(
                    'entri_usada',
                    'entri_usada.id_entri = pengobatan.id_entri'
                )
                ->orderBy('pengobatan.id_pengobatan', 'DESC')
                ->findAll(),

            'daftarPenyakit' => $this->entriUsadaModel
                ->select('
                    entri_usada.id_entri,
                    entri_usada.nama_penyakit,
                    wuku.nama_wuku,
                    saptawara.nama_saptawara,
                    pancawara.nama_pancawara
                ')
                ->join(
                    'wuku',
                    'wuku.id_wuku = entri_usada.id_wuku'
                )
                ->join(
                    'saptawara',
                    'saptawara.id_saptawara = entri_usada.id_saptawara'
                )
                ->join(
                    'pancawara',
                    'pancawara.id_pancawara = entri_usada.id_pancawara'
                )
                ->orderBy('entri_usada.nama_penyakit', 'ASC')
                ->findAll(),
        ];

        return view('admin/pengobatan/index', $data);
    }


    // =========================================
    // MENYIMPAN BEBERAPA PENGOBATAN
    // =========================================

    public function simpan()
    {
        $idEntri = $this->request->getPost('id_entri');
        $daftarPengobatan = $this->request->getPost('pengobatan');

        $errors = [];
        $dataSimpan = [];

        // Fungsi untuk mengambil teks dengan aman
        $ambilTeks = static function ($value) {
            return is_scalar($value)
                ? trim((string) $value)
                : '';
        };

        // Validasi penyakit
        $idValid = filter_var($idEntri, FILTER_VALIDATE_INT);

        if (
            $idValid === false ||
            $idValid <= 0 ||
            !$this->entriUsadaModel->find($idValid)
        ) {
            $errors[] = 'Penyakit yang dipilih tidak valid.';
        }

        // Validasi jumlah kelompok
        if (
            !is_array($daftarPengobatan) ||
            empty($daftarPengobatan)
        ) {
            $errors[] = 'Tambahkan minimal satu kelompok pengobatan.';
        } else {

            // Periksa seluruh kelompok pengobatan
            foreach ($daftarPengobatan as $index => $item) {

                $nomor = $index + 1;

                if (!is_array($item)) {
                    $errors[] = 'Pengobatan ' . $nomor . ': Format data tidak valid.';
                    continue;
                }

                $jenis = $ambilTeks($item['jenis'] ?? '');
                $bahanAsli = $ambilTeks($item['bahan_asli'] ?? '');
                $bagianTubuh = $ambilTeks($item['bagian_tubuh'] ?? '');
                $keterangan = $ambilTeks($item['keterangan'] ?? '');
                $cara = $ambilTeks($item['cara'] ?? '');

                // Validasi Jenis Pengobatan
                if ($jenis === '') {
                    $errors[] = 'Pengobatan ' . $nomor . ': Jenis Pengobatan wajib diisi.';
                } elseif (mb_strlen($jenis) > 100) {
                    $errors[] = 'Pengobatan ' . $nomor . ': Jenis Pengobatan maksimal 100 karakter.';
                }

                // Validasi Bahan Asli
                if ($bahanAsli === '') {
                    $errors[] = 'Pengobatan ' . $nomor . ': Bahan Asli wajib diisi.';
                }

                // Validasi Bagian Tubuh
                if ($bagianTubuh === '') {
                    $errors[] = 'Pengobatan ' . $nomor . ': Bagian Tubuh wajib diisi.';
                }

                // Keterangan bersifat opsional
                if ($keterangan === '') {
                    $keterangan = '-';
                }

                // Validasi Cara Pengobatan
                if ($cara === '') {
                    $errors[] = 'Pengobatan ' . $nomor . ': Cara Pengobatan wajib diisi.';
                }

                $dataSimpan[] = [
                    'id_entri' => $idEntri,
                    'jenis' => $jenis,
                    'bahan_asli' => $bahanAsli,
                    'bagian_tubuh' => $bagianTubuh,
                    'keterangan' => $keterangan,
                    'cara' => $cara,
                ];
            }
        }

        // Jika ada kesalahan, jangan simpan data
        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }


        // =========================================
        // TRANSAKSI PENYIMPANAN
        // =========================================

        $db = \Config\Database::connect();

        $db->transBegin();

        try {

            foreach ($dataSimpan as $data) {

                if (!$this->pengobatanModel->insert($data)) {

                    $modelErrors = $this->pengobatanModel->errors();

                    throw new \RuntimeException(
                        'Insert gagal: ' . json_encode($modelErrors)
                    );
                }
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Transaksi penyimpanan gagal.'
                );
            }

            $db->transCommit();

            return redirect()->to(site_url('admin/pengobatan'))
                ->with(
                    'success',
                    count($dataSimpan) . ' data pengobatan berhasil ditambahkan.'
                );

        } catch (\Throwable $e) {

            $dbError = $db->error();
            $lastQuery = (string) $db->getLastQuery();

            $db->transRollback();

            log_message(
                'error',
                'Gagal menyimpan pengobatan: ' . $e->getMessage()
            );

            log_message(
                'error',
                'Database error: ' . json_encode($dbError)
            );

            log_message(
                'error',
                'Last query: ' . $lastQuery
            );

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Data pengobatan belum berhasil disimpan. Silakan periksa kembali atau coba beberapa saat lagi.'
                );
        }
    }


    // =========================================
    // MEMPERBARUI SATU DATA PENGOBATAN
    // =========================================

    public function update($id)
    {
        $pengobatan = $this->pengobatanModel->find($id);

        if (!$pengobatan) {
            return redirect()->to(site_url('admin/pengobatan'))
                ->with('error', 'Data pengobatan tidak ditemukan.');
        }

        $idEntri = $this->request->getPost('id_entri');
        $daftarPengobatan = $this->request->getPost('pengobatan');

        $errors = [];

        $ambilTeks = static function ($value) {
            return is_scalar($value)
                ? trim((string) $value)
                : '';
        };

        // Validasi penyakit
        $idValid = filter_var($idEntri, FILTER_VALIDATE_INT);

        if (
            $idValid === false ||
            $idValid <= 0 ||
            !$this->entriUsadaModel->find($idValid)
        ) {
            $errors[] = 'Penyakit yang dipilih tidak valid.';
        }

        // Edit hanya mengambil satu kelompok
        $item = is_array($daftarPengobatan)
            ? reset($daftarPengobatan)
            : null;

        if (!is_array($item)) {
            $errors[] = 'Data pengobatan tidak ditemukan.';
            $item = [];
        }

        $jenis = $ambilTeks($item['jenis'] ?? '');
        $bahanAsli = $ambilTeks($item['bahan_asli'] ?? '');
        $bagianTubuh = $ambilTeks($item['bagian_tubuh'] ?? '');
        $keterangan = $ambilTeks($item['keterangan'] ?? '');
        $cara = $ambilTeks($item['cara'] ?? '');

        // Validasi Jenis Pengobatan
        if ($jenis === '') {
            $errors[] = 'Jenis Pengobatan wajib diisi.';
        } elseif (mb_strlen($jenis) > 100) {
            $errors[] = 'Jenis Pengobatan maksimal 100 karakter.';
        }

        // Validasi Bahan Asli
        if ($bahanAsli === '') {
            $errors[] = 'Bahan Asli wajib diisi.';
        }

        // Validasi Bagian Tubuh
        if ($bagianTubuh === '') {
            $errors[] = 'Bagian Tubuh wajib diisi.';
        }

        // Keterangan opsional
        if ($keterangan === '') {
            $keterangan = '-';
        }

        // Validasi Cara Pengobatan
        if ($cara === '') {
            $errors[] = 'Cara Pengobatan wajib diisi.';
        }

        // Jika ada kesalahan
        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }

        $dataUpdate = [
            'id_entri' => $idEntri,
            'jenis' => $jenis,
            'bahan_asli' => $bahanAsli,
            'bagian_tubuh' => $bagianTubuh,
            'keterangan' => $keterangan,
            'cara' => $cara,
        ];

        if ($this->pengobatanModel->update($id, $dataUpdate)) {
            return redirect()->to(site_url('admin/pengobatan'))
                ->with('success', 'Data pengobatan berhasil diperbarui.');
        }

        $db = \Config\Database::connect();

        log_message(
            'error',
            'Gagal memperbarui pengobatan: ' .
            json_encode($db->error())
        );

        return redirect()->back()
            ->withInput()
            ->with(
                'error',
                'Data pengobatan belum berhasil diperbarui. Silakan coba kembali.'
            );
    }


    // =========================================
    // MENGHAPUS DATA PENGOBATAN
    // =========================================

    public function hapus($id)
    {
        $pengobatan = $this->pengobatanModel->find($id);

        if (!$pengobatan) {
            return redirect()->to(site_url('admin/pengobatan'))
                ->with('error', 'Data pengobatan tidak ditemukan.');
        }

        $this->pengobatanModel->delete($id);

        return redirect()->to(site_url('admin/pengobatan'))
            ->with('success', 'Data pengobatan berhasil dihapus.');
    }
}