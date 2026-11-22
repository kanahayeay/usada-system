<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<?php
$keyword = $keyword ?? '';
$kategori = $kategori ?? [];
$filterKategori = $filterKategori ?? '';
$filterBahasa = $filterBahasa ?? '';
$glosarium = $glosarium ?? [];
$pager = $pager ?? service('pager');
?>

<div class="container-fluid admin-glosarium">

    <!-- HEADER -->
    <div class="gl-page-header">
        <div>
            <span class="gl-eyebrow">MANAJEMEN DATA</span>
            <h4>Kelola Glosarium</h4>
            <p>Kelola istilah dan makna dalam Glosarium Usada Bali.</p>
        </div>

        <button type="button"
                class="gl-btn gl-btn-primary"
                id="btnTambahGlosarium">
            <i class="bi bi-plus-lg"></i>
            Tambah Istilah
        </button>
    </div>

  <!-- TOAST NOTIFICATION -->
  <?php
  $success = session()->getFlashdata('success');
  $error   = session()->getFlashdata('error');
  ?>

  <?php if ($success || $error): ?>
      <div class="gl-toast <?= $success ? 'gl-toast-success' : 'gl-toast-error' ?>"
          id="glToast">

          <div class="gl-toast-icon">
              <i class="bi <?= $success ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' ?>"></i>
          </div>

          <div class="gl-toast-content">
              <strong>
                  <?= $success ? 'Berhasil!' : 'Gagal!' ?>
              </strong>
              <p>
                  <?= esc($success ?? $error) ?>
              </p>
          </div>

          <button type="button" class="gl-toast-close" id="glToastClose">
              <i class="bi bi-x-lg"></i>
          </button>

          <div class="gl-toast-progress"></div>
      </div>
  <?php endif; ?>

    <!-- FILTER -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= site_url('admin/glosarium') ?>" method="get">
                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label class="form-label">Cari Istilah</label>
                        <input
                            type="text"
                            name="keyword"
                            class="form-control"
                            placeholder="Cari istilah, varian, atau makna..."
                            value="<?= esc($keyword) ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>

                            <?php foreach ($kategori as $itemKategori): ?>
                                <option
                                    value="<?= esc($itemKategori) ?>"
                                    <?= $filterKategori === $itemKategori ? 'selected' : '' ?>
                                >
                                    <?= esc($itemKategori) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Bahasa</label>
                        <input
                            type="text"
                            name="bahasa"
                            class="form-control"
                            placeholder="Bahasa..."
                            value="<?= esc($filterBahasa) ?>"
                        >
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="gl-btn gl-btn-primary flex-grow-1">
                            <i class="bi bi-search"></i>
                            Cari
                        </button>

                        <a href="<?= site_url('admin/glosarium') ?>"
                           class="gl-btn gl-btn-light">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- DAFTAR GLOSARIUM -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div>
                <h6 class="fw-bold mb-1">Daftar Istilah Glosarium</h6>
                <small class="text-muted">
                    Data istilah yang tersimpan dalam database.
                </small>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No.</th>
                            <th>Istilah</th>
                            <th>Varian</th>
                            <th>Bahasa</th>
                            <th>Kategori</th>
                            <th>Makna Indonesia</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($glosarium)): ?>

                        <?php
                        $nomor = 1 + (10 * ((int) service('pager')->getCurrentPage() - 1));
                        ?>

                        <?php foreach ($glosarium as $item): ?>
                            <tr>
                                <td><?= $nomor++ ?></td>

                                <td class="fw-semibold">
                                    <?= esc($item['istilah']) ?>
                                </td>

                                <td><?= esc($item['varian'] ?: '-') ?></td>
                                <td><?= esc($item['bahasa']) ?></td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($item['kategori']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= esc(mb_strimwidth(
                                        $item['makna_indonesia'],
                                        0,
                                        100,
                                        '...'
                                    )) ?>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">

                                        <!-- EDIT -->
                                        <button
                                            type="button"
                                            class="gl-action-btn gl-edit-btn"
                                            title="Edit istilah"
                                            data-glosarium="<?= esc(json_encode($item), 'attr') ?>"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- HAPUS -->
                                        <form
                                            action="<?= site_url('admin/glosarium/hapus/' . $item['id_glosarium']) ?>"
                                            method="post"
                                            class="gl-delete-form"
                                            data-istilah="<?= esc($item['istilah'], 'attr') ?>"
                                        >   
                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="gl-action-btn gl-delete-btn"
                                                title="Hapus istilah"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                Tidak ada data Glosarium yang ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">
                <small class="text-muted">
                    Menampilkan <?= count($glosarium) ?> data pada halaman ini.
                </small>

                <div>
                    <?= $pager->only(['keyword', 'kategori', 'bahasa'])->links() ?>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- =================================================
     MODAL TAMBAH DAN EDIT GLOSARIUM
================================================== -->

<div class="gl-modal" id="glosariumModal" aria-hidden="true">

    <div class="gl-modal-overlay" data-close-modal></div>

    <div class="gl-modal-dialog" role="dialog"
         aria-modal="true"
         aria-labelledby="glModalTitle">

        <!-- MODAL HEADER -->
        <div class="gl-modal-header">
            <div>
                <span class="gl-eyebrow">GLOSARIUM USADA BALI</span>
                <h4 id="glModalTitle">Tambah Istilah</h4>
                <p id="glModalDescription">
                    Masukkan istilah baru ke dalam Glosarium.
                </p>
            </div>

            <button type="button"
                    class="gl-modal-close"
                    data-close-modal
                    aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- MODAL BODY -->
        <form id="glosariumForm"
              action="<?= site_url('admin/glosarium/simpan') ?>"
              method="post">

            <?= csrf_field() ?>

            <div class="gl-modal-body">

                <div class="gl-form-group">
                    <label for="glIstilah">
                        Istilah <span>*</span>
                    </label>
                    <input
                        type="text"
                        name="istilah"
                        id="glIstilah"
                        class="form-control"
                        placeholder="Masukkan istilah"
                        maxlength="150"
                        required
                    >
                </div>

                <div class="gl-form-row">
                    <div class="gl-form-group">
                        <label for="glVarian">Varian Istilah</label>
                        <input
                            type="text"
                            name="varian"
                            id="glVarian"
                            class="form-control"
                            placeholder="Varian istilah (opsional)"
                            maxlength="150"
                        >
                    </div>

                    <div class="gl-form-group">
                        <label for="glBahasa">
                            Bahasa <span>*</span>
                        </label>
                        <input
                            type="text"
                            name="bahasa"
                            id="glBahasa"
                            class="form-control"
                            placeholder="Contoh: Bali"
                            maxlength="50"
                            required
                        >
                    </div>
                </div>

                <div class="gl-form-group">
                    <label for="glKategori">
                        Kategori <span>*</span>
                    </label>
                    <input
                        type="text"
                        name="kategori"
                        id="glKategori"
                        class="form-control"
                        placeholder="Masukkan kategori istilah"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="gl-form-group">
                    <label for="glMakna">
                        Makna dalam Bahasa Indonesia <span>*</span>
                    </label>
                    <textarea
                        name="makna_indonesia"
                        id="glMakna"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan makna istilah..."
                        required
                    ></textarea>
                </div>

                <div class="gl-form-group">
                    <label for="glContoh">Contoh Konteks</label>
                    <textarea
                        name="contoh_konteks"
                        id="glContoh"
                        class="form-control"
                        rows="2"
                        placeholder="Masukkan contoh penggunaan istilah (opsional)"
                    ></textarea>
                </div>

            </div>

            <!-- MODAL FOOTER -->
            <div class="gl-modal-footer">
                <button type="button"
                        class="gl-btn gl-btn-light"
                        data-close-modal>
                    Batal
                </button>

                <button type="submit"
                        class="gl-btn gl-btn-primary"
                        id="glSubmitButton">
                    <i class="bi bi-check-lg"></i>
                    Simpan Istilah
                </button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div class="gl-modal" id="glDeleteModal" aria-hidden="true">
    <div class="gl-modal-overlay" data-close-delete></div>

    <div class="gl-delete-dialog" role="dialog" aria-modal="true">
        <div class="gl-delete-icon">
            <i class="bi bi-trash"></i>
        </div>

        <h4>Hapus Istilah Glosarium?</h4>

        <p>
            Apakah kamu yakin ingin menghapus istilah
            <strong id="glDeleteName"></strong>?
            Data yang sudah dihapus tidak dapat dikembalikan.
        </p>

        <div class="gl-modal-footer">
            <button type="button"
                    class="gl-btn gl-btn-light"
                    data-close-delete>
                Batal
            </button>

            <button type="button"
                    class="gl-btn gl-btn-danger"
                    id="glConfirmDelete">
                <i class="bi bi-trash"></i>
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script src="<?= base_url('js/admin-glosarium.js') ?>"></script>

<?= $this->endSection() ?>
