<?php
/**
 * @var array $stats
 * @var string $namaAdmin
 */
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="admin-welcome">
    <div>
        <span class="admin-welcome__eyebrow">RINGKASAN SISTEM</span>
        <h2>Selamat datang, <?= esc($namaAdmin ?? 'Admin') ?>!</h2>
        <p>Kelola arsip digital pengetahuan Usada Bali melalui panel administrasi.</p>
    </div>

    <span class="admin-welcome__icon">
        <i class="bi bi-flower1"></i>
    </span>
</div>

<div class="admin-stats">
    <?php foreach ($stats as $stat): ?>
        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <span><?= esc($stat['nama']) ?></span>
                <span class="admin-stat-card__icon">
                    <i class="bi <?= esc($stat['icon']) ?>"></i>
                </span>
            </div>

            <strong><?= number_format($stat['jumlah'], 0, ',', '.') ?></strong>
            <small>Total data tersimpan</small>
        </article>
    <?php endforeach; ?>
</div>

<section class="admin-info-card">
    <div class="admin-info-card__heading">
        <div>
            <span class="admin-welcome__eyebrow">PENGELOLAAN ARSIP</span>
            <h3>Informasi Sistem</h3>
        </div>
        <i class="bi bi-info-circle"></i>
    </div>

    <p>
        Dashboard ini menjadi pusat pengelolaan arsip digital
        Tenung Wraspatikalpa. Gunakan menu di sebelah kiri untuk
        mengelola data setelah fitur terkait tersedia.
    </p>
</section>

<?= $this->endSection() ?>
