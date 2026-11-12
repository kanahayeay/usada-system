<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
// Bangun URL dengan mempertahankan filter yang sedang aktif.
$url = function (array $tambah = []) use ($filter): string {
    $q = array_filter(array_merge($filter, $tambah), static fn($v) => $v !== '' && $v !== null);
    return base_url('struktur' . ($q ? '?' . http_build_query($q) : ''));
};
$u = $terpilih;
?>

<section class="container page-head">
    <span class="badge badge--soft-green"><i class="bi bi-journal-text"></i> Struktur Pengetahuan Naskah Usada</span>
    <h1>Struktur & Detail Informasi Usada</h1>
    <p>Jelajahi hubungan antara penanggalan Pawukon, penyakit, gejala, pengobatan, ritual, dan teks asli dalam <strong>Usada Tenung Wraspatikalpa</strong>.</p>
</section>

<section class="container">
    <div class="card card--white">
        <span class="label label--brown">Sistem Pengetahuan Terpadu</span>
        <h2>Eksplorasi Struktur Usada</h2>
        <p class="muted">Hubungan antar dimensi pengetahuan dalam naskah:</p>
        <div class="flow">
            <?php foreach ($dimensi as $i => [$ikon, $judul, $sub]): ?>
                <?php if ($i > 0): ?><i class="bi bi-arrow-right flow__arrow"></i><?php endif; ?>
                <div class="flow__item <?= $i === $dimensiAktif ? 'is-active' : '' ?>">
                    <i class="bi <?= esc($ikon, 'attr') ?>"></i>
                    <span><strong><?= esc($judul) ?></strong><small><?= esc($sub) ?></small></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="container">
    <form class="filter" method="get" action="<?= base_url('struktur') ?>">
        <div class="row-between">
            <h3><i class="bi bi-sliders"></i> Filter Informasi Usada</h3>
            <a href="<?= base_url('struktur') ?>" class="reset"><i class="bi bi-arrow-repeat"></i> Reset Filter</a>
        </div>
        <div class="filter__grid">
            <?php foreach (['wuku' => 'Siklus Wuku', 'hari' => 'Saptawara (Hari)', 'pancawara' => 'Pancawara (Pasaran)'] as $nama => $label): ?>
                <label><?= esc($label) ?>
                    <select name="<?= esc($nama, 'attr') ?>" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <?php foreach ($opsi[$nama] as $o): ?>
                            <option value="<?= esc($o, 'attr') ?>" <?= $filter[$nama] === $o ? 'selected' : '' ?>><?= esc($o) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endforeach; ?>
            <label>Cari Naskah
                <span class="search"><i class="bi bi-search"></i><input type="text" name="q" value="<?= esc($filter['q'], 'attr') ?>" placeholder="Nama penyakit atau gejala"></span>
            </label>
        </div>
    </form>
</section>

<section class="container" id="daftar">
    <div class="row-between list-head">
        <div>
            <h2>Daftar Informasi Usada</h2>
            <p class="muted">Menampilkan entri Usada yang sesuai dengan filter.</p>
        </div>
        <span class="badge badge--soft-green"><i class="bi bi-archive"></i> <?= esc($totalHasil) ?> dari <?= esc($totalSemua) ?> Entri</span>
    </div>

    <?php if ($daftar): ?>
        <div class="cards">
            <?php foreach ($daftar as $item): ?>
                <?= $this->setVar('u', $item)
                         ->setVar('aktif', $terpilih && $item['id'] === $terpilih['id'])
                         ->setVar('urlDetail', $url(['hal' => $halaman, 'id' => $item['id']]) . '#detail')
                         ->include('partials/usada_card') ?>
            <?php endforeach; ?>
        </div>

        <?php if ($jumlahHalaman > 1): ?>
            <nav class="pager" aria-label="Halaman">
                <?php if ($halaman > 1): ?><a href="<?= esc($url(['hal' => $halaman - 1]) . '#daftar', 'attr') ?>"><i class="bi bi-chevron-left"></i> Sebelumnya</a><?php endif; ?>
                <span>Halaman <?= esc($halaman) ?> dari <?= esc($jumlahHalaman) ?></span>
                <?php if ($halaman < $jumlahHalaman): ?><a href="<?= esc($url(['hal' => $halaman + 1]) . '#daftar', 'attr') ?>">Berikutnya <i class="bi bi-chevron-right"></i></a><?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="card card--mint kosong-state">
            <i class="bi bi-search"></i>
            <h3>Tidak ada entri yang cocok</h3>
            <p class="muted">Coba ubah kata kunci atau reset filter.</p>
            <a href="<?= base_url('struktur') ?>" class="btn btn--primary btn--sm">Reset Filter</a>
        </div>
    <?php endif; ?>
</section>

<?php if ($u): ?>
<section class="container" id="detail">
    <div class="card card--white detail">
        <div class="row-between">
            <a href="#daftar" class="back"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Usada</a>
            <div class="badges">
                <span class="badge badge--brown"><?= esc(strtoupper(lembar_label($u))) ?></span>
                <span class="badge badge--tag">Hari ke-<?= esc($u['hari_ke']) ?> dari 210</span>
            </div>
        </div>

        <div class="row-between detail__title">
            <div>
                <h2 class="display"><?= teks_atau_strip($u['nama_penyakit']) ?></h2>
                <p class="detail__sub">Wuku <?= esc($u['wuku']) ?> · <?= esc($u['hari']) ?> · <?= esc($u['pancawara']) ?></p>
            </div>
            <a href="#transkripsi" class="btn btn--primary btn--sm"><i class="bi bi-book"></i> Baca Naskah Lontar</a>
        </div>

        <div class="detail__grid">
            <div class="stack">
                <div class="box">
                    <h3 class="box__title"><i class="bi bi-activity"></i> Penyakit & Gejala Klinis Lontar</h3>
                    <span class="label label--brown">Gejala yang tertulis:</span>
                    <p><?= teks_atau_strip($u['gejala']) ?></p>
                    <div class="inner inner--tint">
                        <span class="label label--brown">Penyebab:</span>
                        <p class="small"><?= teks_atau_strip($u['penyebab']) ?></p>
                    </div>
                </div>

                <div class="box">
                    <h3 class="box__title"><i class="bi bi-capsule"></i> Pengobatan & Formulasi Herbal</h3>
                    <?php if ($u['pengobatan']): ?>
                        <ul class="obat-list">
                            <?php foreach ($u['pengobatan'] as $p): ?>
                                <?= $this->setVar('p', $p)->include('partials/pengobatan_item') ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="kosong">Tidak ada keterangan pengobatan pada naskah ini.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stack">
                <div class="box">
                    <h3 class="box__title"><i class="bi bi-calendar3"></i> Konteks Penanggalan Kosmologis</h3>
                    <div class="three">
                        <div class="mini"><small>Wuku</small><strong><?= esc($u['wuku']) ?></strong><small>Wuku ke-<?= esc($u['wuku_no']) ?></small></div>
                        <div class="mini"><small>Saptawara</small><strong><?= esc($u['hari']) ?></strong><small><?= esc(nama_saptawara($u['hari'])) ?></small></div>
                        <div class="mini"><small>Pancawara</small><strong><?= esc($u['pancawara']) ?></strong><small><?= esc(arah_pancawara($u['pancawara'])) ?></small></div>
                    </div>
                </div>

                <div class="box">
                    <h3 class="box__title"><i class="bi bi-flower1"></i> Ritual & Upakara Penyeimbang</h3>
                    <p class="<?= $u['ritual'] ? '' : 'kosong' ?>"><?= teks_atau_strip($u['ritual']) ?></p>
                </div>

                <div class="box">
                    <h3 class="box__title"><i class="bi bi-journal-bookmark"></i> Referensi Naskah Lontar</h3>
                    <dl class="refs small">
                        <dt>Naskah:</dt><dd>Usada Tenung Wraspatikalpa</dd>
                        <dt>Posisi lembar:</dt><dd><?= esc(lembar_label($u)) ?></dd>
                        <dt>Urutan pawukon:</dt><dd>Hari ke-<?= esc($u['hari_ke']) ?></dd>
                    </dl>
                    <div class="row-between folio">
                        <?php if ($sebelumnya): ?><a href="<?= esc(base_url('struktur?id=' . $sebelumnya['id']) . '#detail', 'attr') ?>"><i class="bi bi-chevron-left"></i> <?= esc($sebelumnya['wuku']) ?> <?= esc($sebelumnya['hari']) ?></a><?php else: ?><span></span><?php endif; ?>
                        <?php if ($berikutnya): ?><a href="<?= esc(base_url('struktur?id=' . $berikutnya['id']) . '#detail', 'attr') ?>"><?= esc($berikutnya['wuku']) ?> <?= esc($berikutnya['hari']) ?> <i class="bi bi-chevron-right"></i></a><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container" id="transkripsi">
    <div class="card card--mint">
        <span class="label label--green"><i class="bi bi-patch-check"></i> Preservasi filologi autentik</span>
        <h2>Transkripsi Teks Asli Lontar</h2>
        <p class="muted">Alih bahasa naskah sebagaimana tercatat, termasuk penanda lembar.</p>
        <details class="lanjut">
            <summary class="btn btn--inverted"><i class="bi bi-book"></i> Lihat Transkripsi Teks Asli Lontar</summary>
            <div class="teks-asli"><?= esc($u['teks_asli']) ?></div>
        </details>
    </div>
</section>

<section class="container">
    <div class="card card--white">
        <span class="label label--brown">Pemetaan rantai kausalitas</span>
        <h2>Visualisasi Alur Relasi Pengetahuan Usada</h2>
        <p class="muted">Rantai penanggalan, penyakit, pengobatan, dan ritual dalam satu entri:</p>
        <?php
        $rantai = [
            ['Wuku', $u['wuku']],
            ['Saptawara', $u['hari']],
            ['Pancawara', $u['pancawara']],
            ['Penyakit', $u['nama_penyakit'] ?: '-'],
            ['Pengobatan', count($u['pengobatan']) . ' ramuan'],
            ['Ritual', $u['ritual'] ? 'Ada' : '-'],
            ['Lembar', lembar_label($u)],
        ];
        ?>
        <div class="chain">
            <?php foreach ($rantai as $i => [$judul, $isi]): ?>
                <?php if ($i > 0): ?><i class="bi bi-arrow-right flow__arrow"></i><?php endif; ?>
                <div class="chain__item <?= $i === 3 ? 'is-active' : ($i === 6 ? 'is-brown' : '') ?>">
                    <small><?= $i + 1 ?>. <?= esc($judul) ?></small>
                    <strong><?= esc($isi) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>