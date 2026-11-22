<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/admin-entri-usada.css') ?>">

<?php
$keyword = $keyword ?? '';
$entriUsada = $entriUsada ?? [];
$daftarWuku = $daftarWuku ?? [];
$daftarSaptawara = $daftarSaptawara ?? [];
$daftarPancawara = $daftarPancawara ?? [];
$pager = $pager ?? service('pager');

$errors = session()->getFlashdata('errors') ?? [];
?>

<div class="container-fluid admin-entri-usada">

    <!-- HEADER -->
    <div class="gl-page-header">
        <div>
            <span class="gl-eyebrow">MANAJEMEN DATA</span>
            <h4>Data Usada</h4>
            <p>Informasi penyakit dan pengetahuan yang bersumber dari naskah Usada Bali.</p>
        </div>

        <button type="button" class="gl-btn gl-btn-primary"
            onclick="bukaModalTambah()">
            <i class="bi bi-plus-lg"></i>
            Tambah Entri
        </button>
    </div>


    <!-- TOAST NOTIFICATION -->
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

            <button type="button" class="gl-toast-close" id="glToastClose">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="gl-toast-progress"></div>
        </div>
    <?php endif; ?>

    <!-- FILTER -->
    <div class="card gl-search-card">
        <div class="card-body">
            <form action="<?= site_url('admin/entri-usada') ?>" method="get">
                <div class="entri-search-wrapper">

                    <div class="entri-search-input">
                        <label class="form-label">Cari Data Usada</label>
                        <input
                            type="text"
                            name="keyword"
                            class="form-control"
                            placeholder="Cari nama penyakit, gejala, penyebab, atau ritual..."
                            value="<?= esc($keyword) ?>"
                        >
                    </div>

                    <div class="entri-search-actions">
                        <button type="submit" class="gl-btn gl-btn-primary">
                            <i class="bi bi-search"></i>
                            Cari
                        </button>

                        <a href="<?= site_url('admin/entri-usada') ?>"
                           class="gl-btn gl-btn-light">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- DAFTAR DATA -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-1">Daftar Entri Usada</h6>
            <small class="text-muted">
                Data pengetahuan yang bersumber dari naskah Usada Bali.
            </small>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="5%">No.</th>
                            <th>Nama Penyakit</th>
                            <th>Gejala</th>
                            <th>Penyebab</th>
                            <th>Ritual</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($entriUsada)): ?>

                            <?php
                            $nomor = 1 + (10 * ((int) $pager->getCurrentPage() - 1));
                            ?>

                            <?php foreach ($entriUsada as $item): ?>
                                <tr>
                                    <td><?= $nomor++ ?></td>

                                    <td class="fw-semibold">
                                        <?= $item['nama_penyakit'] === '-'
                                            ? '<span class="text-muted fst-italic">Tidak disebutkan</span>'
                                            : esc($item['nama_penyakit']) ?>
                                    </td>

                                    <td>
                                        <?= esc(mb_strimwidth($item['gejala'] ?? '-', 0, 80, '...')) ?>
                                    </td>

                                    <td>
                                        <?= esc(mb_strimwidth($item['penyebab'] ?? '-', 0, 80, '...')) ?>
                                    </td>

                                    <td>
                                        <?= esc(mb_strimwidth($item['ritual'] ?? '-', 0, 80, '...')) ?>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">

                                            <button type="button"
                                                class="entri-action-btn edit"
                                                title="Edit"
                                                onclick='bukaModalEdit(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>)'>
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <button type="button"
                                                class="entri-action-btn delete"
                                                title="Hapus"
                                                onclick='bukaModalHapus(
                                                    <?= (int) $item["id_entri"] ?>,
                                                    <?= json_encode($item["nama_penyakit"], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>
                                                )'>
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                    Tidak ada data Usada yang ditemukan.
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

            <!-- PAGINATION -->
            <div class="admin-table-footer">
                <small class="text-muted">
                    Menampilkan <?= count($entriUsada) ?> data pada halaman ini.
                </small>

                <div>
                    <?= $pager->only(['keyword'])->links() ?>
                </div>
            </div>
        </div>
    </div>

</div>



<!-- MODAL TAMBAH / EDIT -->
<div class="gl-modal" id="entriUsadaModal" aria-hidden="true">

    <div class="gl-modal-overlay" data-close-modal></div>

    <div class="gl-modal-dialog" role="dialog"
         aria-modal="true"
         aria-labelledby="entriUsadaModalTitle">

        <!-- HEADER -->
        <div class="gl-modal-header">
            <div>
                <span class="gl-eyebrow">DATA USADA BALI</span>
                <h4 id="entriUsadaModalTitle">Tambah Entri Usada</h4>
                <p>Lengkapi informasi pengetahuan dari naskah Usada Bali.</p>
            </div>

            <button type="button" class="gl-modal-close"
                    data-close-modal aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- FORM -->
        <form id="entriUsadaForm" method="post"
              action="<?= site_url('admin/entri-usada/simpan') ?>"
              data-save-url="<?= site_url('admin/entri-usada/simpan') ?>"
              data-update-url="<?= site_url('admin/entri-usada/update') ?>">

            <?= csrf_field() ?>

            <div class="gl-modal-body">

                <h6 class="fw-bold mb-3">Informasi Penyakit</h6>

                <div class="gl-form-group">
                    <label for="nama_penyakit">Nama Penyakit</label>
                    <input type="text" name="nama_penyakit"
                           id="nama_penyakit" class="form-control"
                           maxlength="255" required>
                </div>

                <div class="gl-form-row">
                    <div class="gl-form-group">
                        <label for="id_wuku">Wuku</label>
                        <select name="id_wuku" id="id_wuku"
                                class="form-select" required>
                            <option value="">Pilih Wuku</option>
                            <?php foreach ($daftarWuku as $wuku): ?>
                                <option value="<?= esc($wuku['id_wuku']) ?>">
                                    <?= esc($wuku['nama_wuku']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="gl-form-group">
                        <label for="id_saptawara">Saptawara</label>
                        <select name="id_saptawara" id="id_saptawara"
                                class="form-select" required>
                            <option value="">Pilih Saptawara</option>
                            <?php foreach ($daftarSaptawara as $hari): ?>
                                <option value="<?= esc($hari['id_saptawara']) ?>">
                                    <?= esc($hari['nama_saptawara']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="gl-form-group">
                        <label for="id_pancawara">Pancawara</label>
                        <select name="id_pancawara" id="id_pancawara"
                                class="form-select" required>
                            <option value="">Pilih Pancawara</option>
                            <?php foreach ($daftarPancawara as $hari): ?>
                                <option value="<?= esc($hari['id_pancawara']) ?>">
                                    <?= esc($hari['nama_pancawara']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold mb-3">Informasi Pengobatan</h6>

                <div class="gl-form-group">
                    <label for="gejala">Gejala</label>
                    <textarea name="gejala" id="gejala"
                              class="form-control" rows="3" required></textarea>
                </div>

                <div class="gl-form-group">
                    <label for="penyebab">Penyebab</label>
                    <textarea name="penyebab" id="penyebab"
                              class="form-control" rows="3" required></textarea>
                </div>

                <div class="gl-form-group">
                    <label for="ritual">Ritual</label>
                    <textarea name="ritual" id="ritual"
                              class="form-control" rows="3" required></textarea>
                </div>

                <hr>

                <h6 class="fw-bold mb-3">Informasi Sumber Naskah</h6>

                <div class="gl-form-group">
                    <label for="teks_asli">Teks Asli</label>
                    <textarea name="teks_asli" id="teks_asli"
                              class="form-control" rows="4" required></textarea>
                </div>

                <div class="gl-form-row">
                    <div class="gl-form-group">
                        <label for="lembar_awal">Lembar Awal</label>
                        <input type="text" name="lembar_awal"
                               id="lembar_awal" class="form-control"
                               maxlength="10" required>
                    </div>

                    <div class="gl-form-group">
                        <label for="lembar_akhir">Lembar Akhir</label>
                        <input type="text" name="lembar_akhir"
                               id="lembar_akhir" class="form-control"
                               maxlength="10" required>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="gl-modal-footer">
                <button type="button" class="gl-btn gl-btn-light"
                        data-close-modal>
                    Batal
                </button>

                <button type="submit" class="gl-btn gl-btn-primary">
                    <i class="bi bi-save"></i>
                    Simpan Data
                </button>
            </div>

        </form>
    </div>
</div>


<!-- MODAL KONFIRMASI HAPUS -->
<div class="gl-modal" id="hapusEntriModal" aria-hidden="true">

    <div class="gl-modal-overlay" data-close-delete></div>

    <div class="gl-delete-dialog" role="dialog" aria-modal="true">

        <div class="gl-delete-icon">
            <i class="bi bi-trash"></i>
        </div>

        <h4>Hapus Entri Usada?</h4>

        <p>
            Apakah kamu yakin ingin menghapus
            <strong id="namaEntriHapus"></strong>?
            Data yang dihapus tidak dapat dikembalikan.
        </p>

        <form id="hapusEntriForm" method="post"
              data-delete-url="<?= site_url('admin/entri-usada/hapus') ?>">

            <?= csrf_field() ?>

            <div class="gl-modal-footer">

                <button type="button"
                        class="gl-btn gl-btn-light"
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

<script src="<?= base_url('js/admin-entri-usada.js') ?>"></script>

<?= $this->endSection() ?>