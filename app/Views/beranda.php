<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * @var array $stats
 * @var string $tanggal
 * @var bool $adalahHariIni
 * @var array|null $usada
 */
?>

<!-- HERO + SUMBER NASKAH (satu blok) -->
<section class="hero hero--beranda container">

    <div class="hero__main">
        <span class="badge badge--white">
            <i class="bi bi-patch-check"></i>
            Kearifan Naskah Lontar Medis Tradisional Bali
        </span>

        <h1>Eksplorasi Kearifan Medis Lontar Usada Wraspatikalpa</h1>

        <p>
            Naskah pengobatan tradisional Bali yang memadukan perhitungan
            kalender pawukon dengan diagnosis penyakit, gejala fisik,
            formula ramuan herbal nusantara, serta sarana ritual
            penyeimbang yang tertulis dalam lontar klasik.
        </p>

        <p class="hero__stats">
            <span class="label-inline">
                <i class="bi bi-bar-chart"></i> Arsip Digital:
            </span>

            <?php foreach ($stats as $s): ?>
                <span>
                    <strong><?= esc($s['angka']) ?></strong>
                    <?= esc($s['nama']) ?>
                </span>
            <?php endforeach; ?>
        </p>
    </div>

    <aside class="hero__source">
        <span class="label-inline">
            <i class="bi bi-journal-bookmark"></i>
            MENGENAL NASKAH
        </span>

        <p>
            Arsip digital ini bersumber dari naskah
            <strong>Tenung Wraspatikalpa</strong>,
            koleksi lontar UPTD Gedong Kirtya, Singaraja.
        </p>

        <div class="hero__details">
            <div>
                <span class="label">Nomor Koleksi</span>
                <strong>IIIa.249/8</strong>
            </div>
            <div>
                <span class="label">Tanggal Penurunan</span>
                <strong>1 April – 7 Juni 1973</strong>
            </div>
            <div>
                <span class="label">Penurun</span>
                <strong>I Ketut Mangku Ngarsa</strong>
            </div>
            <div>
                <span class="label">Pemeriksa</span>
                <strong>I Ketut Suwidja</strong>
            </div>
            <div>
                <span class="label">Pengetik Kembali</span>
                <strong>I Gede Suparna</strong>
            </div>
            <div>
                <span class="label">Diketik Kembali</span>
                <strong>24 Juli 1988</strong>
            </div>
        </div>
    </aside>

</section>

<!-- PENCARIAN TANGGAL -->
<section class="container search-date">
    <label for="tanggal" class="search-date__label">
        <i class="bi bi-calendar3"></i>
        Cari Tanggal Lontar
    </label>

    <form class="search-date__form" action="<?= base_url('/') ?>" method="get">
        <input
            type="date"
            id="tanggal"
            name="tanggal"
            class="input-pill"
            value="<?= esc($tanggal, 'attr') ?>"
        >

        <button type="submit" class="btn btn--primary">
            Cari Data Usada
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>
</section>


<!-- PENANGGALAN PAWUKON -->
<?php if ($usada): ?>
<section class="container">
    <div class="today-card">

        <div class="today-card__head">
            <h2>
                <i class="bi bi-calendar3"></i>
                Penanggalan Wariga & Lontar
                <?= $adalahHariIni ? 'Hari Ini' : 'Tanggal Terpilih' ?>
            </h2>

            <span class="badge badge--soft">
                <?= esc(tanggal_indonesia($tanggal)) ?>
            </span>
        </div>

        <div class="today-card__grid">
            <div>
                <span class="label">Wuku</span>
                <strong><?= esc($usada['wuku']) ?></strong>
                <span class="chip">
                    Wuku ke-<?= esc($usada['wuku_no']) ?>
                </span>
            </div>

            <div>
                <span class="label">Saptawara</span>
                <strong><?= esc($usada['hari']) ?></strong>
                <span class="chip">
                    <?= esc(nama_saptawara($usada['hari'])) ?>
                </span>
            </div>

            <div>
                <span class="label">Pancawara</span>
                <strong><?= esc($usada['pancawara']) ?></strong>
                <span class="chip">
                    Pancawara
                </span>
            </div>
        </div>

    </div>
</section>


<!-- RINGKASAN USADA -->
<section class="container usada-today">

    <h2 class="section-title">
        <span class="dot"></span>
        Usada <?= $adalahHariIni ? 'Hari Ini' : 'Tanggal Terpilih' ?>
    </h2>

    <p class="section-sub">
        Catatan pengobatan berdasarkan kalkulasi pawukon
    </p>

    <article class="panel">

        <span class="badge badge--brown">
            <?= esc(strtoupper(lembar_label($usada))) ?>
        </span>

        <hr>

        <div class="info-grid">

            <div class="info-box">
                <span class="label label--brown">
                    <i class="bi bi-bug"></i>
                    Penyakit / Keluhan
                </span>

                <h3><?= teks_atau_strip($usada['nama_penyakit']) ?></h3>
            </div>

            <div class="info-box">
                <span class="label label--brown">
                    <i class="bi bi-heart-pulse"></i>
                    Gejala yang Timbul
                </span>

                <p class="clamp clamp-4">
                    <?= teks_atau_strip($usada['gejala']) ?>
                </p>
            </div>

            <div class="info-box">
                <span class="label label--green">
                    <i class="bi bi-capsule"></i>
                    Pengobatan & Bahan Herbal
                </span>

                <?php if ($usada['pengobatan']): ?>
                    <ul class="obat-list">
                        <?php foreach (array_slice($usada['pengobatan'], 0, 2) as $p): ?>
                            <?= $this->setVar('p', $p)->include('partials/pengobatan_item') ?>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="kosong">-</p>
                <?php endif; ?>
            </div>

            <div class="info-box">
                <span class="label label--brown">
                    <i class="bi bi-flower1"></i>
                    Upakara / Ritual Penyeimbang
                </span>

                <p class="clamp clamp-4">
                    <?= teks_atau_strip($usada['ritual']) ?>
                </p>
            </div>

        </div>

        <hr>

        <div class="panel__foot">
            <a
                href="<?= base_url('struktur?id=' . $usada['id']) ?>#detail"
                class="btn btn--secondary btn--sm"
            >
                Lihat Detail Usada Lengkap
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    </article>
</section>
<?php endif; ?>

<?= $this->endSection() ?>