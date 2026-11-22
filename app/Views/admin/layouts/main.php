<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= esc($title ?? 'Admin') ?> | Usada Bali</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar">

            <a href="<?= site_url('admin') ?>" class="admin-brand">
                <span class="admin-brand__icon">
                    <i class="bi bi-journal-richtext"></i>
                </span>

                <span>
                    <strong>Usada Bali</strong>
                    <small>Administration</small>
                </span>
            </a>

            <div class="admin-sidebar__label">
                MENU UTAMA
            </div>

            <nav class="admin-nav">

                <!-- Dashboard -->
                <a
                    href="<?= site_url('admin') ?>"
                    class="admin-nav__link <?= ($active ?? '') === 'dashboard' ? 'is-active' : '' ?>"
                >
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>

                <!-- Glosarium -->
                <a
                    href="<?= site_url('admin/glosarium') ?>"
                    class="admin-nav__link <?= ($active ?? '') === 'glosarium' ? 'is-active' : '' ?>"
                >
                    <i class="bi bi-book-half"></i>
                    Glosarium
                </a>

                <!-- Data Usada -->
                <a
                    href="<?= site_url('admin/entri-usada') ?>"
                    class="admin-nav__link <?= ($active ?? '') === 'entri-usada' ? 'is-active' : '' ?>"
                >
                    <i class="bi bi-journal-text"></i>
                    Data Usada
                </a>

                <!-- Pengobatan -->
                <a
                    href="#"
                    class="admin-nav__link is-disabled"
                    aria-disabled="true"
                >
                    <i class="bi bi-capsule"></i>
                    Pengobatan
                    <small>Segera</small>
                </a>

                <!-- Akun Admin -->
                <a
                    href="#"
                    class="admin-nav__link is-disabled"
                    aria-disabled="true"
                >
                    <i class="bi bi-people"></i>
                    Akun Admin
                    <small>Segera</small>
                </a>

            </nav>

            <!-- SIDEBAR BOTTOM -->
            <div class="admin-sidebar__bottom">

                <a
                    href="<?= base_url('/') ?>"
                    target="_blank"
                    rel="noopener"
                    class="admin-nav__link"
                >
                    <i class="bi bi-box-arrow-up-right"></i>
                    Lihat Website
                </a>

                <form action="<?= site_url('admin/logout') ?>" method="post">
                    <?= csrf_field() ?>

                    <button type="submit" class="admin-nav__link admin-logout">
                        <i class="bi bi-box-arrow-left"></i>
                        Keluar
                    </button>
                </form>

            </div>

        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main">

            <!-- TOPBAR -->
            <header class="admin-topbar">
                <div>
                    <span class="admin-topbar__eyebrow">
                        USADA BALI
                    </span>

                    <h1>Panel Administrasi</h1>
                </div>

                <div class="admin-profile">

                    <span class="admin-profile__icon">
                        <i class="bi bi-person"></i>
                    </span>

                    <span>
                        <strong><?= esc($namaAdmin ?? 'Admin') ?></strong>
                        <small>Administrator</small>
                    </span>

                </div>

            </header>

            <!-- PAGE CONTENT -->
            <section class="admin-content">
                <?= $this->renderSection('content') ?>
            </section>

        </main>

    </div>

</body>
</html>