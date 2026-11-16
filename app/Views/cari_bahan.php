<?php

$keyword = $keyword ?? '';
$halaman = $halaman ?? 1;
$jumlahHalaman = $jumlahHalaman ?? 1;
$hasil = $hasil ?? [];
$total = $total ?? 0;
?>

<?php
/**
 * @var string $title
 * @var string $active
 * @var string $keyword
 * @var int $total
 * @var array $hasil
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container cari-bahan-page">

    <!-- Hero -->
    <section class="hero">
        <span class="label label--green">Kearifan Herbal Naskah Usada</span>

        <h1>Cari Bahan Herbal</h1>

        <p>
            Temukan bahan herbal yang tercatat dalam naskah
            <strong>Usada Tenung Wraspatikalpa</strong> dan temukan penggunaannya dalam pengobatan tradisional Bali.
        </p>

        <!-- Form pencarian -->
        <form
            class="cari-bahan__form"
            action="<?= base_url('/cari-bahan') ?>"
            method="get"
        >
            <div class="cari-bahan__input">
                <i class="bi bi-search"></i>

                <input
                    type="search"
                    name="q"
                    value="<?= esc($keyword) ?>"
                    placeholder="Contoh: Sirih, Kunir, Temu..."
                    aria-label="Cari bahan herbal"
                >
            </div>

            <button type="submit" class="btn btn--primary">
                Cari Bahan
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <?php if ($keyword !== ''): ?>
            <div class="cari-bahan__status">
                <i class="bi bi-check-circle"></i>

                Menampilkan
                <strong><?= $total ?></strong>
                entri Usada yang menggunakan bahan
                <strong>"<?= esc($keyword) ?>"</strong>.
            </div>
        <?php endif; ?>
    </section>


    <!-- Hasil pencarian -->
    <?php if ($keyword !== ''): ?>

        <section class="cari-bahan__results">

            <div class="list-head cari-bahan__results-head">
                <div>
                    <span class="eyebrow">HASIL PENCARIAN</span>
                    <h2>Usada & Penyakit yang Menggunakan Bahan Ini</h2>
                </div>

                <?php if ($jumlahHalaman > 1): ?>
                    <div class="cari-bahan__pagination">

                        <div class="cari-bahan__pagination-pages">
                            <?php
                            $jumlahTampil = 5;

                            $awalHalaman = max(1, $halaman - 2);
                            $akhirHalaman = min($jumlahHalaman, $awalHalaman + $jumlahTampil - 1);

                            if (($akhirHalaman - $awalHalaman + 1) < $jumlahTampil) {
                                $awalHalaman = max(1, $akhirHalaman - $jumlahTampil + 1);
                            }
                            ?>

                            <?php if ($halaman > 1): ?>
                                <a
                                    class="cari-bahan__pagination-btn"
                                    href="<?= base_url('/cari-bahan?q=' . urlencode($keyword) . '&hal=' . ($halaman - 1)) ?>"
                                    aria-label="Halaman sebelumnya"
                                >
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <span class="cari-bahan__pagination-btn is-disabled" aria-hidden="true">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            <?php endif; ?>

                            <?php for ($i = $awalHalaman; $i <= $akhirHalaman; $i++): ?>
                                <a
                                    class="cari-bahan__pagination-number <?= $i === $halaman ? 'is-active' : '' ?>"
                                    href="<?= base_url('/cari-bahan?q=' . urlencode($keyword) . '&hal=' . $i) ?>"
                                >
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($halaman < $jumlahHalaman): ?>
                                <a
                                    class="cari-bahan__pagination-btn"
                                    href="<?= base_url('/cari-bahan?q=' . urlencode($keyword) . '&hal=' . ($halaman + 1)) ?>"
                                    aria-label="Halaman berikutnya"
                                >
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <span class="cari-bahan__pagination-btn is-disabled" aria-hidden="true">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($hasil)): ?>

                <div class="cari-bahan__cards">

                    <?php foreach ($hasil as $entri): ?>

                        <?php
                        /*
                         * Cari pengobatan yang benar-benar mengandung
                         * keyword yang dicari.
                         */
                        $pengobatanCocok = [];

                        foreach ($entri['pengobatan'] ?? [] as $obat) {
                            if (
                                $keyword !== '' &&
                                isset($obat['bahan']) &&
                                stripos($obat['bahan'], $keyword) !== false
                            ) {
                                $pengobatanCocok[] = $obat;
                            }
                        }
                        ?>

                        <article class="cari-bahan-card">

                            <!-- Referensi naskah -->
                            <div class="cari-bahan-card__meta">
                                <span>
                                    <i class="bi bi-file-earmark-text"></i>

                                    LEMBAR
                                    <?= esc($entri['lembar_awal'] ?: '-') ?>
                                    <?= $entri['lembar_akhir']
                                        ? '–' . esc($entri['lembar_akhir'])
                                        : '' ?>
                                </span>

                                <span>
                                    Naskah Usada Tenung Wraspatikalpa
                                </span>
                            </div>


                            <!-- Nama penyakit -->
                            <div class="cari-bahan-card__title">
                                <span class="badge badge--brown cari-bahan-card__disease-name">
                                    <?= esc(
                                        $entri['nama_penyakit']
                                        ?: 'Nama penyakit tidak tersedia'
                                    ) ?>
                                </span>
                            </div>


                            <!-- Gejala -->
                            <?php if (!empty($entri['gejala'])): ?>
                                <div class="cari-bahan-card__section">

                                    <span class="label">
                                        <i class="bi bi-activity"></i>
                                        Gejala
                                    </span>

                                    <p>
                                        <?= esc($entri['gejala']) ?>
                                    </p>

                                </div>
                            <?php endif; ?>


                            <!-- Bahan yang cocok -->
                            <?php if (!empty($pengobatanCocok)): ?>

                                <div class="cari-bahan-card__match">

                                    <div class="cari-bahan-card__match-head">
                                        <div>
                                            <span class="label label--green">
                                                Bahan Ditemukan
                                            </span>

                                            <h4>
                                                <i class="bi bi-flower1"></i>
                                                Bahan yang digunakan
                                            </h4>
                                        </div>

                                        <span class="badge badge--soft-green">
                                            <?= count($pengobatanCocok) ?>
                                            kecocokan
                                        </span>
                                    </div>


                                    <?php foreach ($pengobatanCocok as $obat): ?>

                                        <div class="cari-bahan-match">

                                            <div class="cari-bahan-match__icon">
                                                <i class="bi bi-leaf"></i>
                                            </div>

                                            <div class="cari-bahan-match__content">

                                                <div class="cari-bahan-match__main">
                                                    <?= preg_replace(
                                                        '/(' . preg_quote($keyword, '/') . ')/i',
                                                        '<strong>$1</strong>',
                                                        esc($obat['bahan'])
                                                    ) ?>
                                                </div>

                                                <?php if (!empty($obat['cara'])): ?>
                                                    <div class="cari-bahan-match__cara">
                                                        <span>
                                                            <i class="bi bi-arrow-right-circle"></i>
                                                            Cara pengobatan
                                                        </span>

                                                        <strong>
                                                            <?= esc($obat['cara']) ?>
                                                        </strong>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($obat['bagian_tubuh'])): ?>
                                                    <small>
                                                        Bagian tubuh:
                                                        <?= esc($obat['bagian_tubuh']) ?>
                                                    </small>
                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            <?php endif; ?>


                            <!-- Pengobatan lainnya -->
                            <?php
                            $pengobatanLain = [];

                            foreach ($entri['pengobatan'] ?? [] as $obat) {
                                if (
                                    !in_array($obat, $pengobatanCocok, true)
                                ) {
                                    $pengobatanLain[] = $obat;
                                }
                            }
                            ?>

                            <?php if (!empty($pengobatanLain)): ?>

                                <details class="cari-bahan-card__other">

                                    <summary>
                                        <span>
                                            <i class="bi bi-capsule"></i>
                                            Pengobatan lainnya
                                        </span>

                                        <i class="bi bi-chevron-down"></i>
                                    </summary>

                                    <div class="obat-list">

                                        <?php foreach ($pengobatanLain as $obat): ?>

                                            <div class="obat">

                                                <?php if (!empty($obat['jenis'])): ?>
                                                    <span class="badge badge--tag">
                                                        <?= esc($obat['jenis']) ?>
                                                    </span>
                                                <?php endif; ?>

                                                <div class="obat__isi">

                                                    <?php if (!empty($obat['bahan'])): ?>
                                                        <p>
                                                            <?= esc($obat['bahan']) ?>
                                                        </p>
                                                    <?php endif; ?>

                                                    <?php if (!empty($obat['cara'])): ?>
                                                        <small class="muted">
                                                            Cara:
                                                            <?= esc($obat['cara']) ?>
                                                        </small>
                                                    <?php endif; ?>

                                                </div>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                </details>

                            <?php endif; ?>


                            <!-- Ritual -->
                            <?php if (!empty($entri['ritual'])): ?>

                                <div class="cari-bahan-card__ritual">

                                    <span class="label label--brown">
                                        <i class="bi bi-stars"></i>
                                        Ritual
                                    </span>

                                    <p>
                                        <?= esc($entri['ritual']) ?>
                                    </p>

                                </div>

                            <?php endif; ?>


                            <!-- Teks asli -->
                            <?php if (!empty($entri['teks_asli'])): ?>

                                <details class="lanjut cari-bahan-card__source">

                                    <summary class="btn btn--outlined btn--sm">
                                        <i class="bi bi-file-text"></i>
                                        Lihat Teks Asli Lontar
                                    </summary>

                                    <div class="teks-asli">
                                        <?= esc($entri['teks_asli']) ?>
                                    </div>

                                </details>

                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <!-- Tidak ada hasil -->
                <div class="card card--white kosong-state">

                    <i class="bi bi-search"></i>

                    <h3>
                        Bahan tidak ditemukan
                    </h3>

                    <p class="muted">
                        Belum ditemukan entri Usada yang menggunakan
                        bahan <strong>"<?= esc($keyword) ?>"</strong>.
                    </p>

                    <a
                        href="<?= base_url('/cari-bahan') ?>"
                        class="btn btn--primary"
                    >
                        Cari Bahan Lain
                    </a>

                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>