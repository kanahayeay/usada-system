<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/admin-pengobatan.css') ?>">

<?php
$pengobatan = $pengobatan ?? [];
$daftarPenyakit = $daftarPenyakit ?? [];
$errors = session()->getFlashdata('errors') ?? [];
?>

<div class="container-fluid admin-pengobatan">

    <!-- HEADER -->
    <div class="gl-page-header">
        <div>
            <span class="gl-eyebrow">MANAJEMEN DATA</span>
            <h4>Pengobatan</h4>
            <p>Informasi pengobatan penyakit berdasarkan pengetahuan dari naskah Usada Bali.</p>
        </div>

        <button type="button" class="gl-btn gl-btn-primary"
            onclick="bukaModalTambah()">
            <i class="bi bi-plus-lg"></i>
            Tambah Pengobatan
        </button>
    </div>

 

    <!-- =========================================
        TOAST NOTIFICATION
    ========================================= -->

    <?php
        $success = session()->getFlashdata('success');
        $error = session()->getFlashdata('error');
    ?>

    <?php if ($success || $error): ?>

        <div class="gl-toast <?= $success ? 'gl-toast-success' : 'gl-toast-error' ?>"
            id="glToast">

            <div class="gl-toast-icon">
                <i class="bi <?= $success
                    ? 'bi-check-circle-fill'
                    : 'bi-exclamation-circle-fill' ?>"></i>
            </div>

            <div class="gl-toast-content">
                <strong>
                    <?= $success ? 'Berhasil!' : 'Gagal!' ?>
                </strong>

                <p><?= esc($success ?? $error) ?></p>
            </div>

            <button type="button"
                    class="gl-toast-close"
                    id="glToastClose"
                    aria-label="Tutup notifikasi">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="gl-toast-progress"></div>

        </div>

    <?php endif; ?>

    <!-- DETAIL VALIDASI -->
    <?php if (!empty($errors)): ?>

        <div class="pengobatan-validation">
            <div class="pengobatan-validation-header">
                <i class="bi bi-exclamation-triangle-fill"></i>

                <div>
                    <strong>Data belum lengkap</strong>
                    <p>Periksa kembali informasi pengobatan berikut:</p>
                </div>
            </div>

            <ul>
                <?php foreach ($errors as $message): ?>
                    <li><?= esc($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php endif; ?>

    <!-- DAFTAR DATA -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-1">Daftar Pengobatan</h6>
            <small class="text-muted">
                Data pengobatan yang terhubung dengan informasi penyakit pada Data Usada.
            </small>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="5%">No.</th>
                            <th>Nama Penyakit</th>
                            <th>Jenis Pengobatan</th>
                            <th>Bahan Asli</th>
                            <th>Bagian Tubuh</th>
                            <th>Keterangan / Kondisi</th>
                            <th>Cara Pengobatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($pengobatan)): ?>

                            <?php $nomor = 1; ?>

                            <?php foreach ($pengobatan as $item): ?>
                                <tr>
                                    <td><?= $nomor++ ?></td>

                                    <td class="fw-semibold">
                                        <?= esc($item['nama_penyakit'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= esc($item['jenis'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= esc(mb_strimwidth(
                                            $item['bahan_asli'] ?? '-',
                                            0,
                                            70,
                                            '...'
                                        )) ?>
                                    </td>

                                    <td>
                                        <?= esc(mb_strimwidth(
                                            $item['bagian_tubuh'] ?? '-',
                                            0,
                                            50,
                                            '...'
                                        )) ?>
                                    </td>

                                    <td>
                                        <?= esc($item['keterangan'] ?: '-') ?>
                                    </td>

                                    <td>
                                        <?= esc(mb_strimwidth(
                                            $item['cara'] ?? '-',
                                            0,
                                            70,
                                            '...'
                                        )) ?>
                                    </td>

                                    <td class="text-center">
                                       <div class="d-flex justify-content-center align-items-center gap-2">
                                            <button type="button"
                                                class="entri-action-btn edit"
                                                title="Edit"
                                                onclick='bukaModalEdit(<?= json_encode(
                                                    $item,
                                                    JSON_HEX_APOS | JSON_HEX_QUOT |
                                                    JSON_HEX_TAG | JSON_HEX_AMP
                                                ) ?>)'>
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <button type="button"
                                                class="entri-action-btn delete"
                                                title="Hapus"
                                                onclick='bukaModalHapus(
                                                    <?= (int) $item["id_pengobatan"] ?>,
                                                    <?= json_encode(
                                                        $item["nama_penyakit"] . " - " . $item["jenis"],
                                                        JSON_HEX_APOS | JSON_HEX_QUOT |
                                                        JSON_HEX_TAG | JSON_HEX_AMP
                                                    ) ?>
                                                )'>
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                    Belum ada data pengobatan yang tersedia.
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

            <div class="admin-table-footer">
                <small class="text-muted">
                    Menampilkan <?= count($pengobatan) ?> data pengobatan.
                </small>
            </div>
        </div>
    </div>

</div>


<!-- MODAL TAMBAH / EDIT -->
<div class="gl-modal" id="pengobatanModal" aria-hidden="true">

    <div class="gl-modal-overlay" data-close-modal></div>

    <div class="gl-modal-dialog">

        <form id="pengobatanForm"
            method="post"
            action="<?= site_url('admin/pengobatan/simpan') ?>"
            data-save-url="<?= site_url('admin/pengobatan/simpan') ?>"
            data-update-url="<?= site_url('admin/pengobatan/update') ?>">

            <?= csrf_field() ?>

            <div class="gl-modal-header">
                <div>
                    <h5 class="gl-modal-title" id="pengobatanModalTitle">
                        Tambah Pengobatan
                    </h5>
                    <small class="text-muted">
                        Lengkapi informasi pengobatan berdasarkan naskah Usada Bali.
                    </small>
                </div>

                <button type="button" class="gl-modal-close"
                    data-close-modal aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="gl-modal-body">

                <!-- INFORMASI PENYAKIT -->
                <h6 class="fw-bold mb-3">Informasi Penyakit</h6>

                <div class="mb-3">
                    <label for="id_entri" class="form-label">
                        Nama Penyakit
                    </label>

                    <select name="id_entri" id="id_entri"
                        class="form-select" required>

                        <option value="">Pilih Penyakit</option>

                        <?php foreach ($daftarPenyakit as $penyakit): ?>
                            <option value="<?= esc($penyakit['id_entri']) ?>">
                                <?= esc($penyakit['nama_penyakit']) ?>
                                — Wuku: <?= esc($penyakit['nama_wuku']) ?>
                                | Saptawara: <?= esc($penyakit['nama_saptawara']) ?>
                                | Pancawara: <?= esc($penyakit['nama_pancawara']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <hr>

                <!-- INFORMASI PENGOBATAN -->
                <div class="pengobatan-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">Informasi Pengobatan</h6>
                            <small class="text-muted">
                                Tambahkan satu atau lebih jenis pengobatan untuk penyakit yang dipilih.
                            </small>
                        </div>
                    </div>

                    <!-- WADAH KELOMPOK PENGOBATAN -->
                    <div id="daftarPengobatan">

                        <!-- KELOMPOK PENGOBATAN PERTAMA DIBUAT OLEH JAVASCRIPT -->

                    </div>

                    <!-- TOMBOL TAMBAH -->
                    <button type="button"
                        class="gl-btn gl-btn-light w-100 mt-3"
                        id="tambahKelompokPengobatan">
                        <i class="bi bi-plus-circle"></i>
                        Tambah Pengobatan
                    </button>

                    <!-- JUMLAH PENGOBATAN -->
                    <div class="text-end mt-3">
                        <small class="text-muted" id="jumlahPengobatan">
                            1 pengobatan
                        </small>
                    </div>
                </div>
            </div>

            <div class="gl-modal-footer">
                <button type="button" class="gl-btn gl-btn-light"
                    data-close-modal>
                    Batal
                </button>

                <button type="submit" class="gl-btn gl-btn-primary"
                    id="btnSimpanPengobatan">
                    <i class="bi bi-save"></i>
                    Simpan Semua Pengobatan
                </button>
            </div>

        </form>
    </div>
</div>


<!-- MODAL KONFIRMASI HAPUS -->
<div class="gl-modal" id="hapusPengobatanModal" aria-hidden="true">

    <div class="gl-modal-overlay" data-close-delete></div>

    <div class="gl-delete-dialog" role="dialog" aria-modal="true">

        <div class="gl-delete-icon">
            <i class="bi bi-trash"></i>
        </div>

        <h4>Hapus Pengobatan?</h4>

        <p>
            Apakah kamu yakin ingin menghapus data pengobatan
            <strong id="namaPengobatanHapus"></strong>?
            Data yang dihapus tidak dapat dikembalikan.
        </p>

        <form id="hapusPengobatanForm" method="post"
            data-delete-url="<?= site_url('admin/pengobatan/hapus') ?>">

            <?= csrf_field() ?>

            <div class="gl-modal-footer justify-content-center">

                <button type="button" class="gl-btn gl-btn-light"
                    data-close-delete>
                    Batal
                </button>

                <button type="submit" class="gl-btn gl-btn-danger">
                    <i class="bi bi-trash"></i>
                    Ya, Hapus
                </button>

            </div>
        </form>

    </div>
</div>


<script src="<?= base_url('js/admin-pengobatan.js') ?>"></script>

<?= $this->endSection() ?>