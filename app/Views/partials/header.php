<?php
$active = $active ?? '';
$menu = [
    'beranda'  => ['Beranda', '/'],
    'bahan'    => ['Cari Bahan', '/cari-bahan'],
    'struktur' => ['Struktur & Detail Usada', '/struktur'],
    'statistik'=> ['Statistik', '/statistik'],
    'glosarium'=> ['Glosarium', '/glosarium'],
];
?>
<header class="site-header">
    <a class="brand" href="<?= base_url('/') ?>">
        <img
            src="<?= base_url('widyasada.png') ?>"
            alt="Logo Widyasada"
            class="brand__logo"
        >

        <div class="brand__text">
            <span class="brand__name">Widyasada</span>
            <span class="brand__sub">Arsip Digital Pengetahuan Lontar Usada Bali</span>
        </div>
    </a>

    <nav class="nav-pill" aria-label="Menu utama">
        <?php foreach ($menu as $key => [$label, $url]): ?>
            <a href="<?= base_url($url) ?>" class="nav-pill__link <?= $active === $key ? 'is-active' : '' ?>">
                <?= esc($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <a href="<?= site_url('admin/login') ?>"
        class="icon-btn icon-btn--brown"
        aria-label="Login Admin"
        title="Login Admin">
            <i class="bi bi-person"></i>
    </a>
</header>