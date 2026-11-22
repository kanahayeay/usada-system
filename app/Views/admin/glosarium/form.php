
<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<?php
$item = $item ?? [];

$isEdit = ($mode ?? 'tambah') === 'edit';

$action = $isEdit
    ? site_url('admin/glosarium/update/' . $item['id_glosarium'])
    : site_url('admin/glosarium/simpan');
?>

<div class="container-fluid admin-glosarium"> 

    <!-- Header -->
    <div class="mb-4">
        <a href="<?= site_url('admin/glosarium') ?>"
           class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Glosarium
        </a>

        <h4 class="fw-bold mt-2 mb-1">
            <?= $isEdit ? 'Edit Istilah Glosarium' : 'Tambah Istilah Glosarium' ?>
        </h4>

        <p class="text-muted mb-0">
            <?= $isEdit
                ? 'Perbarui informasi istilah Glosarium.'
                : 'Masukkan istilah baru ke dalam Glosarium Usada Bali.' ?>
        </p>
    </div>

    <!-- Notifikasi Validasi -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">

                    <!-- Istilah -->
                    <div class="col-md-6">
                        <label for="istilah" class="form-label fw-semibold">
                            Istilah <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="istilah"
                            id="istilah"
                            class="form-control"
                            placeholder="Masukkan istilah"
                            value="<?= esc(old('istilah', $item['istilah'] ?? '')) ?>"
                            required
                        >
                    </div>

                    <!-- Varian -->
                    <div class="col-md-6">
                        <label for="varian" class="form-label fw-semibold">
                            Varian Istilah
                        </label>

                        <input
                            type="text"
                            name="varian"
                            id="varian"
                            class="form-control"
                            placeholder="Masukkan varian istilah (opsional)"
                            value="<?= esc(old('varian', $item['varian'] ?? '')) ?>"
                        >
                    </div>

                    <!-- Bahasa -->
                    <div class="col-md-6">
                        <label for="bahasa" class="form-label fw-semibold">
                            Bahasa <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="bahasa"
                            id="bahasa"
                            class="form-control"
                            placeholder="Contoh: Bali, Jawa, Sanskerta"
                            value="<?= esc(old('bahasa', $item['bahasa'] ?? '')) ?>"
                            required
                        >
                    </div>

                    <!-- Kategori -->
                    <div class="col-md-6">
                        <label for="kategori" class="form-label fw-semibold">
                            Kategori <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="kategori"
                            id="kategori"
                            class="form-control"
                            placeholder="Masukkan kategori istilah"
                            value="<?= esc(old('kategori', $item['kategori'] ?? '')) ?>"
                            required
                        >
                    </div>

                    <!-- Makna Indonesia -->
                    <div class="col-12">
                        <label for="makna_indonesia" class="form-label fw-semibold">
                            Makna dalam Bahasa Indonesia
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="makna_indonesia"
                            id="makna_indonesia"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan makna istilah dalam Bahasa Indonesia"
                            required
                        ><?= esc(old('makna_indonesia', $item['makna_indonesia'] ?? '')) ?></textarea>
                    </div>

                    <!-- Contoh Konteks -->
                    <div class="col-12">
                        <label for="contoh_konteks" class="form-label fw-semibold">
                            Contoh Konteks
                        </label>

                        <textarea
                            name="contoh_konteks"
                            id="contoh_konteks"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan contoh penggunaan istilah (opsional)"
                        ><?= esc(old('contoh_konteks', $item['contoh_konteks'] ?? '')) ?></textarea>
                    </div>

                </div>

                <hr class="my-4">

                <!-- Tombol -->
                <div class="d-flex justify-content-end gap-2">

                    <a href="<?= site_url('admin/glosarium') ?>"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Istilah' ?>
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
