
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="container page-head">
    <span class="badge badge--soft-green">
        <i class="bi bi-journal-text"></i> Dokumentasi Istilah Naskah Usada
    </span>
    <h1>Glosarium Usada Bali</h1>
    <p>
        Kenali istilah, variasi bahasa, dan makna dalam naskah
        <strong>Usada Tenung Wraspatikalpa</strong>.
    </p>
</section>

<section class="container">
    <form class="filter" method="get" action="<?= base_url('glosarium') ?>">
        <div class="row-between">
            <h3><i class="bi bi-search"></i> Cari Istilah Glosarium</h3>
            <a href="<?= base_url('glosarium') ?>" class="reset">
                <i class="bi bi-arrow-repeat"></i> Reset
            </a>
        </div>

        <div class="filter__grid">
            <label>
                Kata Kunci
                <span class="search">
                    <i class="bi bi-search"></i>
                    <input
                        type="text"
                        name="keyword"
                        value="<?= esc($keyword ?? '', 'attr') ?>"
                        placeholder="Cari istilah atau maknanya..."
                    >
                </span>
            </label>

            <label>
                Kategori Istilah
                <select name="kategori" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori as $k): ?>
                        <option
                            value="<?= esc($k, 'attr') ?>"
                            <?= ($kategoriDipilih ?? '') === $k ? 'selected' : '' ?>
                        >
                            <?= esc($k) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </form>
</section>

<section class="container" id="daftar">
    <div class="row-between list-head">
        <div>
            <h2>Daftar Istilah</h2>
            <p class="muted">
                Istilah yang ditemukan berdasarkan pencarian dan kategori.
            </p>
        </div>

        <span class="badge badge--soft-green">
            <i class="bi bi-book"></i>
            <?= count($glosarium) ?> Istilah
        </span>
    </div>

    <?php if ($glosarium): ?>
        <div class="cards">
            <?php foreach ($glosarium as $item): ?>
                <article class="card card--white">
                    <div class="row-between">
                        <span class="badge badge--brown">
                            <?= esc($item['kategori']) ?>
                        </span>
                        <small class="muted">
                            <?= esc($item['bahasa']) ?>
                        </small>
                    </div>

                    <h3><?= esc($item['istilah']) ?></h3>

                    <?php if (!empty($item['varian'])): ?>
                        <p class="muted small">
                            Varian: <?= esc($item['varian']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="inner inner--tint">
                        <span class="label label--brown">Makna</span>
                        <p><?= esc($item['makna_indonesia']) ?></p>
                    </div>

                    <?php if (!empty($item['contoh_konteks'])): ?>
                        <div class="mt-3">
                            <span class="label label--brown">
                                Contoh Konteks
                            </span>
                            <p class="small">
                                <?= esc($item['contoh_konteks']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card card--mint kosong-state">
            <i class="bi bi-search"></i>
            <h3>Istilah tidak ditemukan</h3>
            <p class="muted">
                Coba gunakan kata kunci atau kategori lainnya.
            </p>
            <a href="<?= base_url('glosarium') ?>" class="btn btn--primary btn--sm">
                Reset Pencarian
            </a>
        </div>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
