<?php
/** Kartu daftar. Butuh $u (satu entri), $aktif (bool), $urlDetail (string). */
?>
<article class="usada-card <?= $aktif ? 'is-active' : '' ?>">
    <div class="row-between">
        <span class="badge badge--tag">Wuku <?= esc($u['wuku']) ?></span>
        <span class="badge badge--peach"><?= esc(lembar_label($u)) ?></span>
    </div>
    <h3><?= teks_atau_strip($u['nama_penyakit']) ?></h3>
    <p class="usada-card__meta"><i class="bi bi-calendar3"></i> <?= esc($u['hari']) ?> · <?= esc($u['pancawara']) ?></p>
    <p class="usada-card__text clamp clamp-3"><?= teks_atau_strip($u['gejala']) ?></p>
    <?php if ($aktif): ?>
        <span class="usada-card__state"><i class="bi bi-check-circle"></i> Sedang ditampilkan di bawah</span>
    <?php else: ?>
        <a class="usada-card__btn" href="<?= esc($urlDetail, 'attr') ?>">Lihat Detail Usada <i class="bi bi-arrow-right"></i></a>
    <?php endif; ?>
</article>