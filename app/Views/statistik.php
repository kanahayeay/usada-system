<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
/**
 * Halaman Statistik Usada.
 * Seluruh nilai statistik berasal dari hasil agregasi controller/model.
 */

$ringkasan = $ringkasan ?? [];
$distribusiPengobatan = $distribusi_pengobatan ?? [];
$jenisPengobatan = $jenis_pengobatan ?? [];
$kelengkapan = $kelengkapan ?? [];
$opsi = $opsi ?? [];
$filter = $filter ?? [];
$temuanStatistik = $temuan_statistik ?? [];

$totalEntri = (int) ($ringkasan['total_entri'] ?? 0);
$totalWuku = (int) ($ringkasan['total_wuku'] ?? 0);
$totalSaptawara = (int) ($ringkasan['total_saptawara'] ?? 0);
$totalPancawara = (int) ($ringkasan['total_pancawara'] ?? 0);
$totalPengobatan = (int) ($ringkasan['total_pengobatan'] ?? 0);

$filterWuku = (string) ($filter['wuku'] ?? '');
$filterSaptawara = (string) ($filter['saptawara'] ?? '');
$filterPancawara = (string) ($filter['pancawara'] ?? '');

$adaFilter = $filterWuku !== '' || $filterSaptawara !== '' || $filterPancawara !== '';

$formatAngka = static fn ($angka): string => number_format((int) $angka, 0, ',', '.');

$kapitalAwal = static function ($teks): string {
    $teks = trim((string) $teks);
    if ($teks === '') {
        return $teks;
    }

    if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
        return mb_strtoupper(mb_substr($teks, 0, 1, 'UTF-8'), 'UTF-8')
            . mb_substr($teks, 1, null, 'UTF-8');
    }

    return ucfirst($teks);
};

$persentase = static function ($bagian, $total): int {
    $total = (int) $total;
    if ($total <= 0) {
        return 0;
    }

    return (int) round(((int) $bagian / $total) * 100);
};

$nilaiTerbesar = static function (array $items, string $kolom): int {
    $terbesar = 0;

    foreach ($items as $item) {
        $terbesar = max($terbesar, (int) ($item[$kolom] ?? 0));
    }

    return $terbesar;
};

$itemTerbanyak = static function (array $items, string $labelKey, string $nilaiKey): ?array {
    $terpilih = null;
    $nilaiMaksimum = 0;

    foreach ($items as $item) {
        $nilai = (int) ($item[$nilaiKey] ?? 0);

        if ($nilai > $nilaiMaksimum) {
            $nilaiMaksimum = $nilai;
            $terpilih = [
                'label' => (string) ($item[$labelKey] ?? ''),
                'nilai' => $nilai,
            ];
        }
    }

    return $terpilih;
};

$daftarWuku = $distribusiPengobatan['wuku'] ?? [];
$daftarSaptawara = $distribusiPengobatan['saptawara'] ?? [];
$daftarPancawara = $distribusiPengobatan['pancawara'] ?? [];

// Temuan statistik memakai agregasi keseluruhan data, terpisah dari filter.
$daftarTemuan = $temuanStatistik['distribusi_pengobatan'] ?? [];
$terbanyakWuku = $itemTerbanyak($daftarTemuan['wuku'] ?? [], 'nama_dimensi', 'jumlah_pengobatan');
$terbanyakSaptawara = $itemTerbanyak($daftarTemuan['saptawara'] ?? [], 'nama_dimensi', 'jumlah_pengobatan');
$terbanyakPancawara = $itemTerbanyak($daftarTemuan['pancawara'] ?? [], 'nama_dimensi', 'jumlah_pengobatan');
$terbanyakJenis = $itemTerbanyak($temuanStatistik['jenis_pengobatan'] ?? [], 'jenis', 'jumlah');

$kelengkapanFields = [
    'nama_penyakit' => 'Nama penyakit',
    'penyebab' => 'Penyebab',
    'ritual' => 'Ritual',
    'pengobatan' => 'Catatan pengobatan',
];
?>

<section class="container page-head stat-page-head">
    <span class="badge badge--soft-green">
        <i class="bi bi-bar-chart-line"></i>
        Statistik Arsip Digital
    </span>
    <h1>Statistik Data Usada</h1>
    <p>
        Ringkasan dan persebaran informasi pengobatan dalam naskah
        <strong>Usada Tenung Wraspatikalpa</strong>, berdasarkan data yang telah
        terdokumentasi dan dimensi penanggalan Pawukon.
    </p>
</section>

<section class="container stat-insights" aria-labelledby="insight-statistik">
    <div class="card card--mint">
        <span class="label label--green"><i class="bi bi-lightbulb"></i> Temuan dari seluruh data</span>
        <h2 id="insight-statistik">Temuan Statistik</h2>

        <div class="stat-insight-grid">
            <?php if ($terbanyakWuku): ?>
                <div class="stat-insight">
                    <i class="bi bi-calendar-week"></i>
                    <div>
                        <span class="label">Wuku dengan catatan pengobatan terbanyak</span>
                        <strong><?= esc($terbanyakWuku['label']) ?></strong>
                        <p><?= $formatAngka($terbanyakWuku['nilai']) ?> catatan pengobatan</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($terbanyakSaptawara): ?>
                <div class="stat-insight">
                    <i class="bi bi-calendar-day"></i>
                    <div>
                        <span class="label">Saptawara dengan catatan terbanyak</span>
                        <strong><?= esc($terbanyakSaptawara['label']) ?></strong>
                        <p><?= $formatAngka($terbanyakSaptawara['nilai']) ?> catatan pengobatan</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($terbanyakPancawara): ?>
                <div class="stat-insight">
                    <i class="bi bi-calendar3"></i>
                    <div>
                        <span class="label">Pancawara dengan catatan terbanyak</span>
                        <strong><?= esc($terbanyakPancawara['label']) ?></strong>
                        <p><?= $formatAngka($terbanyakPancawara['nilai']) ?> catatan pengobatan</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($terbanyakJenis): ?>
                <div class="stat-insight">
                    <i class="bi bi-capsule"></i>
                    <div>
                        <span class="label">Jenis pengobatan paling sering tercatat</span>
                        <strong><?= esc($kapitalAwal($terbanyakJenis['label'])) ?></strong>
                        <p><?= $formatAngka($terbanyakJenis['nilai']) ?> catatan</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$terbanyakWuku && !$terbanyakSaptawara && !$terbanyakPancawara && !$terbanyakJenis): ?>
                <p class="kosong">Belum ada data yang cukup untuk membentuk temuan statistik.</p>
            <?php endif; ?>
        </div>
    </div>
</section>


<section class="container" aria-labelledby="filter-statistik">
    <form class="filter stat-filter" method="get" action="<?= base_url('statistik') ?>">
        <div class="row-between">
            <div>
                <h3 id="filter-statistik"><i class="bi bi-sliders"></i> Filter Statistik</h3>
                <p class="muted small">Pilih dimensi Pawukon untuk melihat statistik pada kelompok data tertentu.</p>
            </div>
            <?php if ($adaFilter): ?>
                <a href="<?= base_url('statistik') ?>" class="reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                </a>
            <?php endif; ?>
        </div>

        <div class="filter__grid stat-filter__grid">
            <label for="filter-wuku">
                Wuku
                <select id="filter-wuku" name="wuku">
                    <option value="">Semua Wuku</option>
                    <?php foreach (($opsi['wuku'] ?? []) as $item): ?>
                        <?php $nama = (string) ($item['nama_wuku'] ?? ''); ?>
                        <option value="<?= esc($nama, 'attr') ?>" <?= $filterWuku === $nama ? 'selected' : '' ?>>
                            <?= esc($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label for="filter-saptawara">
                Saptawara
                <select id="filter-saptawara" name="saptawara">
                    <option value="">Semua Saptawara</option>
                    <?php foreach (($opsi['saptawara'] ?? []) as $item): ?>
                        <?php $nama = (string) ($item['nama_saptawara'] ?? ''); ?>
                        <option value="<?= esc($nama, 'attr') ?>" <?= $filterSaptawara === $nama ? 'selected' : '' ?>>
                            <?= esc($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label for="filter-pancawara">
                Pancawara
                <select id="filter-pancawara" name="pancawara">
                    <option value="">Semua Pancawara</option>
                    <?php foreach (($opsi['pancawara'] ?? []) as $item): ?>
                        <?php $nama = (string) ($item['nama_pancawara'] ?? ''); ?>
                        <option value="<?= esc($nama, 'attr') ?>" <?= $filterPancawara === $nama ? 'selected' : '' ?>>
                            <?= esc($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="stat-filter__action">
                <span class="label">Terapkan pilihan</span>
                <button type="submit" class="btn btn--primary btn--sm">
                    <i class="bi bi-funnel"></i> Terapkan Filter
                </button>
            </div>
        </div>

        <?php if ($adaFilter): ?>
            <div class="stat-active-filters">
                <span class="label">Filter aktif:</span>
                <?php if ($filterWuku !== ''): ?>
                    <span class="badge badge--soft-green">Wuku: <?= esc($filterWuku) ?></span>
                <?php endif; ?>
                <?php if ($filterSaptawara !== ''): ?>
                    <span class="badge badge--soft-green">Saptawara: <?= esc($filterSaptawara) ?></span>
                <?php endif; ?>
                <?php if ($filterPancawara !== ''): ?>
                    <span class="badge badge--soft-green">Pancawara: <?= esc($filterPancawara) ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </form>
</section>

<section class="container stat-summary" aria-labelledby="ringkasan-statistik">
    <div class="row-between list-head">
        <div>
            <h2 id="ringkasan-statistik">Ringkasan Data</h2>
            <p class="muted">Jumlah data berdasarkan filter yang sedang diterapkan.</p>
        </div>
        <?php if ($adaFilter): ?>
            <span class="badge badge--peach"><i class="bi bi-filter"></i> Hasil terfilter</span>
        <?php else: ?>
            <span class="badge badge--soft-green"><i class="bi bi-database"></i> Seluruh data</span>
        <?php endif; ?>
    </div>

    <div class="cards stat-metric-grid">
        <article class="card card--white stat-metric">
            <span class="stat-metric__icon"><i class="bi bi-journal-text"></i></span>
            <span class="label">Total Entri Usada</span>
            <strong><?= $formatAngka($totalEntri) ?></strong>
            <small>Catatan entri dalam naskah</small>
        </article>

        <article class="card card--white stat-metric">
            <span class="stat-metric__icon"><i class="bi bi-calendar-week"></i></span>
            <span class="label">Wuku Tercakup</span>
            <strong><?= $formatAngka($totalWuku) ?></strong>
            <small>Dimensi Wuku pada hasil</small>
        </article>

        <article class="card card--white stat-metric">
            <span class="stat-metric__icon"><i class="bi bi-calendar-day"></i></span>
            <span class="label">Saptawara Tercakup</span>
            <strong><?= $formatAngka($totalSaptawara) ?></strong>
            <small>Dimensi Saptawara pada hasil</small>
        </article>

        <article class="card card--white stat-metric">
            <span class="stat-metric__icon"><i class="bi bi-calendar3"></i></span>
            <span class="label">Pancawara Tercakup</span>
            <strong><?= $formatAngka($totalPancawara) ?></strong>
            <small>Dimensi Pancawara pada hasil</small>
        </article>

        <article class="card card--white stat-metric">
            <span class="stat-metric__icon"><i class="bi bi-capsule"></i></span>
            <span class="label">Catatan Pengobatan</span>
            <strong><?= $formatAngka($totalPengobatan) ?></strong>
            <small>Rekaman pengobatan terkait entri</small>
        </article>
    </div>
</section>

<?php if ($totalEntri > 0): ?>

<section class="container stat-distributions" aria-labelledby="distribusi-pengobatan">
    <div class="list-head">
        <h2 id="distribusi-pengobatan">Distribusi Pengobatan Berdasarkan Pawukon</h2>
        <p class="muted">
            Jumlah catatan pengobatan yang terhubung dengan entri pada setiap dimensi kalender.
        </p>
    </div>

    <div class="stat-chart-grid">
        <?php
        $kelompokDistribusi = [
            [
                'key' => 'wuku',
                'judul' => 'Berdasarkan Wuku',
                'ikon' => 'bi-calendar-week',
                'data' => $daftarWuku,
            ],
            [
                'key' => 'saptawara',
                'judul' => 'Berdasarkan Saptawara',
                'ikon' => 'bi-calendar-day',
                'data' => $daftarSaptawara,
            ],
            [
                'key' => 'pancawara',
                'judul' => 'Berdasarkan Pancawara',
                'ikon' => 'bi-calendar3',
                'data' => $daftarPancawara,
            ],
        ];
        ?>

        <?php foreach ($kelompokDistribusi as $kelompok): ?>
            <?php
            $items = $kelompok['data'];
            $maksimum = $nilaiTerbesar($items, 'jumlah_pengobatan');
            ?>
            <article class="card card--white stat-chart-card stat-chart-card--<?= esc($kelompok['key'], 'attr') ?>">
                <div class="row-between stat-chart-card__head">
                    <h3><i class="bi <?= esc($kelompok['ikon'], 'attr') ?>"></i> <?= esc($kelompok['judul']) ?></h3>
                    <span class="badge badge--tag"><?= $formatAngka(count($items)) ?> kategori</span>
                </div>

                <?php if ($items): ?>
                    <div class="stat-bar-list" role="list">
                        <?php foreach ($items as $item): ?>
                            <?php
                            $jumlah = (int) ($item['jumlah_pengobatan'] ?? 0);
                            $nama = (string) ($item['nama_dimensi'] ?? '-');
                            $lebar = $maksimum > 0 ? round(($jumlah / $maksimum) * 100, 1) : 0;
                            ?>
                            <div class="stat-bar-item" role="listitem">
                                <div class="row-between stat-bar-item__label">
                                    <span><?= esc($nama) ?></span>
                                    <strong><?= $formatAngka($jumlah) ?></strong>
                                </div>
                                <div class="stat-bar" role="img" aria-label="<?= esc($nama) ?>: <?= $formatAngka($jumlah) ?> pengobatan">
                                    <div class="stat-bar__fill" style="width: <?= esc((string) $lebar, 'attr') ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="kosong-state stat-empty">
                        <i class="bi bi-bar-chart"></i>
                        <p>Tidak ada data distribusi untuk filter ini.</p>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="container stat-types" aria-labelledby="jenis-pengobatan">
    <div class="card card--white">
        <div class="row-between list-head">
            <div>
                <h2 id="jenis-pengobatan">Jenis Pengobatan</h2>
                <p class="muted">Jumlah catatan pengobatan berdasarkan jenis yang tercatat dalam naskah.</p>
            </div>
            <span class="badge badge--soft-green">
                <i class="bi bi-capsule"></i> <?= $formatAngka(count($jenisPengobatan)) ?> jenis tercatat
            </span>
        </div>

        <?php if ($jenisPengobatan): ?>
            <?php $maksJenis = $nilaiTerbesar($jenisPengobatan, 'jumlah'); ?>
            <div class="stat-type-list">
                <?php foreach ($jenisPengobatan as $item): ?>
                    <?php
                    $jenis = $kapitalAwal($item['jenis'] ?? 'Tidak diketahui');
                    $jumlah = (int) ($item['jumlah'] ?? 0);
                    $lebar = $maksJenis > 0 ? round(($jumlah / $maksJenis) * 100, 1) : 0;
                    ?>
                    <div class="stat-type-item">
                        <div class="row-between stat-type-item__label">
                            <span><?= esc($jenis) ?></span>
                            <strong><?= $formatAngka($jumlah) ?></strong>
                        </div>
                        <div class="stat-bar" role="img" aria-label="<?= esc($jenis) ?>: <?= $formatAngka($jumlah) ?> catatan">
                            <div class="stat-bar__fill stat-bar__fill--brown" style="width: <?= esc((string) $lebar, 'attr') ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="kosong-state stat-empty">
                <i class="bi bi-capsule"></i>
                <h3>Belum ada jenis pengobatan</h3>
                <p class="muted">Tidak ditemukan catatan jenis pengobatan pada data dengan filter ini.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="container stat-completeness" aria-labelledby="kelengkapan-data">
    <div class="card card--white">
        <div class="list-head">
            <h2 id="kelengkapan-data">Kelengkapan Data Entri</h2>
            <p class="muted">
                Perbandingan atribut yang telah dan belum memiliki keterangan pada entri hasil filter.
            </p>
        </div>

        <div class="stat-completeness-list">
            <?php foreach ($kelengkapanFields as $key => $label): ?>
                <?php
                $lengkap = (int) ($kelengkapan[$key]['lengkap'] ?? 0);
                $kosong = (int) ($kelengkapan[$key]['kosong'] ?? 0);
                $persen = $persentase($lengkap, $totalEntri);
                ?>
                <article class="stat-completeness-item">
                    <div class="row-between stat-completeness-item__head">
                        <h3><?= esc($label) ?></h3>
                        <strong><?= $persen ?>%</strong>
                    </div>
                    <div class="stat-completeness-bar" role="img" aria-label="<?= esc($label) ?>: <?= $formatAngka($lengkap) ?> lengkap dan <?= $formatAngka($kosong) ?> kosong">
                        <div class="stat-completeness-bar__complete" style="width: <?= esc((string) $persen, 'attr') ?>%"></div>
                    </div>
                    <div class="row-between stat-completeness-item__meta">
                        <span><i class="bi bi-check-circle"></i> <?= $formatAngka($lengkap) ?> lengkap</span>
                        <span><i class="bi bi-dash-circle"></i> <?= $formatAngka($kosong) ?> kosong</span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php else: ?>

<section class="container">
    <div class="card card--mint kosong-state stat-no-data">
        <i class="bi bi-search"></i>
        <h2>Tidak ada data untuk filter ini</h2>
        <p class="muted">Kombinasi Wuku, Saptawara, dan Pancawara yang dipilih tidak menghasilkan entri Usada.</p>
        <a href="<?= base_url('statistik') ?>" class="btn btn--primary btn--sm">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
        </a>
    </div>
</section>

<?php endif; ?>

<?= $this->endSection() ?>