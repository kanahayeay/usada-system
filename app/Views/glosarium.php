<?php
/**
 * Glosarium Usada Bali
 * Data: $grup (per huruf), $total, $perKategori, $perBahasa, $keyword, $kategori, $bahasa
 */

// Label & ikon kategori (slug di database -> tampilan)
$kategoriMeta = [
    'penyakit'          => ['Penyakit',          'bi-heart-pulse'],
    'bahan_obat'        => ['Bahan Obat',        'bi-flower1'],
    'gejala'            => ['Gejala',            'bi-activity'],
    'metode_pengobatan' => ['Metode Pengobatan', 'bi-droplet-half'],
    'sarana_upacara'    => ['Sarana Upacara',    'bi-fire'],
    'penyebab'          => ['Penyebab',          'bi-question-circle'],
    'istilah_umum'      => ['Istilah Umum',      'bi-chat-quote'],
    'hari'              => ['Hari',              'bi-calendar-event'],
    'pancawara'         => ['Pancawara',         'bi-calendar3'],
    'anatomi'           => ['Anatomi',           'bi-person'],
];
$labelKategori = static fn(string $s): string => $kategoriMeta[$s][0] ?? ucwords(str_replace('_', ' ', $s));
$ikonKategori  = static fn(string $s): string => $kategoriMeta[$s][1] ?? 'bi-bookmark';

// Sorot istilah di dalam cuplikan naskah (aman: teks di-escape dulu)
$sorot = static function (string $teks, array $kata): string {
    $aman = esc($teks);
    $kata = array_values(array_filter(array_map('trim', $kata), 'strlen'));
    if (!$kata) {
        return $aman;
    }
    usort($kata, static fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
    $pola = implode('|', array_map(static fn($k) => preg_quote(esc($k), '/'), $kata));

    return preg_replace('/(?<!\p{L})(' . $pola . ')(?!\p{L})/iu', '<mark>$1</mark>', $aman) ?? $aman;
};

$semuaHuruf = range('A', 'Z');
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/glosarium.css') ?>">

<!-- ============ HERO + PENCARIAN ============ -->
<section class="container gl-hero-wrap">
    <div class="gl-hero">
        <span class="badge badge--soft-green">
            <i class="bi bi-journal-text"></i> Kamus Istilah Naskah Usada
        </span>
        <h1>Glosarium Usada Bali</h1>
        <p>
            Telusuri istilah Bali dan Kawi beserta maknanya dalam naskah
            <strong>Usada Tenung Wraspatikalpa</strong>, lengkap dengan cuplikan kalimat aslinya.
        </p>

        <form class="gl-search" id="gl-form" role="search" autocomplete="off" onsubmit="return false;">
            <label class="gl-search__field" for="gl-q">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input
                    id="gl-q"
                    type="search"
                    name="keyword"
                    value="<?= esc($keyword, 'attr') ?>"
                    placeholder="Cari istilah, varian, atau maknanya… (contoh: loloh, jamu, lambung)"
                    aria-label="Cari istilah glosarium"
                >
                <button type="button" class="gl-search__clear" id="gl-clear" aria-label="Hapus pencarian" hidden>
                    <i class="bi bi-x-lg"></i>
                </button>
            </label>
        </form>

        <ul class="gl-stats" aria-label="Ringkasan glosarium">
            <li><i class="bi bi-book"></i> <strong><?= $total ?></strong> Istilah</li>
            <li><i class="bi bi-tags"></i> <strong><?= count($perKategori) ?></strong> Kategori</li>
            <li><i class="bi bi-translate"></i> <strong><?= count($perBahasa) ?></strong> Bahasa</li>
        </ul>
    </div>
</section>

<!-- ============ FILTER + NAVIGASI ABJAD (A-Z sticky) ============ -->
<section class="gl-toolbar" id="gl-toolbar">
    <div class="container">
        <div class="gl-filters">
            <div class="gl-chips" role="group" aria-label="Filter kategori" id="gl-kategori">
                <button type="button" class="gl-chip is-active" data-kat="">
                    Semua <span><?= $total ?></span>
                </button>
                <?php foreach ($perKategori as $slug => $jumlah): ?>
                    <button type="button" class="gl-chip" data-kat="<?= esc($slug, 'attr') ?>">
                        <i class="bi <?= $ikonKategori($slug) ?>"></i>
                        <?= esc($labelKategori($slug)) ?> <span><?= $jumlah ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="gl-lang-filter" role="group" aria-label="Filter bahasa" id="gl-bahasa">
                <span class="gl-lang-filter__label">Bahasa</span>
                <button type="button" class="gl-seg is-active" data-bhs="">Semua</button>
                <?php foreach ($perBahasa as $nama => $jumlah): ?>
                    <button type="button" class="gl-seg" data-bhs="<?= esc($nama, 'attr') ?>"><?= esc($nama) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<div class="gl-azbar">
    <div class="container">
        <nav class="gl-az" aria-label="Lompat ke huruf" id="gl-az">
            <?php foreach ($semuaHuruf as $h): ?>
                <?php if (isset($grup[$h])): ?>
                    <a href="#huruf-<?= $h ?>" data-huruf="<?= $h ?>"><?= $h ?></a>
                <?php else: ?>
                    <span class="is-empty" aria-hidden="true"><?= $h ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
</div>

<!-- ============ DAFTAR ISTILAH ============ -->
<section class="container gl-list" id="daftar">
    <div class="gl-result">
        <p id="gl-result-text" aria-live="polite">
            Menampilkan <strong><?= $total ?></strong> istilah
        </p>
        <button type="button" class="gl-reset" id="gl-reset" hidden>
            <i class="bi bi-arrow-repeat"></i> Reset filter
        </button>
    </div>

    <?php foreach ($grup as $huruf => $daftar): ?>
        <section class="gl-group" id="huruf-<?= esc($huruf, 'attr') ?>" data-huruf="<?= esc($huruf, 'attr') ?>">
            <header class="gl-group__head">
                <span class="gl-letter" aria-hidden="true"><?= esc($huruf) ?></span>
                <span class="gl-group__line"></span>
                <span class="gl-group__count"><b><?= count($daftar) ?></b> istilah</span>
            </header>

            <div class="gl-grid">
                <?php foreach ($daftar as $item): ?>
                    <?php
                    $varian = array_values(array_filter(array_map('trim', explode('|', (string) ($item['varian'] ?? '')))));
                    $cari   = mb_strtolower($item['istilah'] . ' ' . implode(' ', $varian) . ' ' . $item['makna_indonesia'], 'UTF-8');
                    $bhs    = strtolower($item['bahasa']);
                    ?>
                    <article
                        class="gl-entry"
                        data-kat="<?= esc($item['kategori'], 'attr') ?>"
                        data-bhs="<?= esc($item['bahasa'], 'attr') ?>"
                        data-cari="<?= esc($cari, 'attr') ?>"
                    >
                        <div class="gl-entry__top">
                            <span class="gl-tag">
                                <i class="bi <?= $ikonKategori($item['kategori']) ?>"></i>
                                <?= esc($labelKategori($item['kategori'])) ?>
                            </span>
                            <span class="gl-lang gl-lang--<?= esc($bhs, 'attr') ?>"><?= esc($item['bahasa']) ?></span>
                        </div>

                        <h3 class="gl-entry__term"><?= esc($item['istilah']) ?></h3>

                        <?php if ($varian): ?>
                            <p class="gl-entry__alt">
                                <span>Varian:</span>
                                <?php foreach ($varian as $v): ?>
                                    <em><?= esc($v) ?></em>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>

                        <p class="gl-entry__meaning"><?= esc($item['makna_indonesia']) ?></p>

                        <?php if (!empty($item['contoh_konteks'])): ?>
                            <figure class="gl-quote">
                                <figcaption><i class="bi bi-quote"></i> Cuplikan naskah</figcaption>
                                <blockquote><?= $sorot($item['contoh_konteks'], array_merge([$item['istilah']], $varian)) ?></blockquote>
                            </figure>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <!-- Tampil saat tidak ada hasil (diatur oleh JS) -->
    <div class="card card--mint kosong-state gl-empty" id="gl-empty" hidden>
        <i class="bi bi-search"></i>
        <h3>Istilah tidak ditemukan</h3>
        <p class="muted">Coba kata kunci lain, atau ganti filter kategori dan bahasa.</p>
        <button type="button" class="btn btn--primary btn--sm" id="gl-empty-reset">Reset Pencarian</button>
    </div>
</section>

<button type="button" class="gl-top" id="gl-top" aria-label="Kembali ke atas" hidden>
    <i class="bi bi-arrow-up"></i>
</button>

<script>
    window.GLOSARIUM_INIT = {
        keyword:  <?= json_encode($keyword) ?>,
        kategori: <?= json_encode($kategori) ?>,
        bahasa:   <?= json_encode($bahasa) ?>
    };
</script>
<script src="<?= base_url('js/glosarium.js') ?>" defer></script>

<?= $this->endSection() ?>