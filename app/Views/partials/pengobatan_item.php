<?php
/** Satu baris pengobatan. Butuh variabel $p: jenis, bahan, bagian_tubuh, keterangan, cara. */
?>
<li class="obat">
    <span class="badge badge--tag"><?= esc($p['jenis']) ?></span>
    <div class="obat__isi">
        <p><?= teks_atau_strip($p['bahan']) ?></p>
        <?php if ($p['bagian_tubuh'] || $p['cara'] || $p['keterangan']): ?>
            <small class="muted">
                <?php if ($p['bagian_tubuh']): ?><i class="bi bi-geo-alt"></i> <?= esc($p['bagian_tubuh']) ?><?php endif; ?>
                <?php if ($p['cara']): ?> &nbsp;<i class="bi bi-hand-index"></i> <?= esc($p['cara']) ?><?php endif; ?>
                <?php if ($p['keterangan']): ?><br><i class="bi bi-info-circle"></i> <?= esc($p['keterangan']) ?><?php endif; ?>
            </small>
        <?php endif; ?>
    </div>
</li>