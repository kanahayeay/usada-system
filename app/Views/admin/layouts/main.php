
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

    <!-- HAMBURGER MENU RESPONSIVE -->
    <style>
        /* =====================================
           TOPBAR LEFT
        ===================================== */

        .admin-topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        /* =====================================
           HAMBURGER BUTTON
        ===================================== */

        .admin-menu-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;

            width: 42px;
            height: 42px;

            padding: 0;
            border: 1px solid #E1EBE4;
            border-radius: 10px;

            background: #FFFFFF;
            color: #315C43;

            font-size: 25px;
            cursor: pointer;

            transition: all .2s ease;
        }

        .admin-menu-toggle:hover {
            background: #EDF4EE;
            color: #1B4332;
        }

        /* =====================================
           SIDEBAR OVERLAY
        ===================================== */

        .admin-sidebar-overlay {
            display: none;
        }

        /* =====================================
           RESPONSIVE MOBILE / TABLET
        ===================================== */

        @media (max-width: 768px) {

            /* Hamburger muncul */
            .admin-menu-toggle {
                display: inline-flex;
            }

            /* Sidebar menjadi drawer */
            .admin-sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                bottom: 0 !important;

                width: min(280px, 85vw) !important;
                height: 100dvh !important;

                z-index: 10001 !important;

                transform: translateX(-100%);
                visibility: hidden;

                overflow-x: hidden;
                overflow-y: auto;

                transition:
                    transform .3s ease,
                    visibility .3s ease;
            }

            .admin-sidebar.is-open {
                transform: translateX(0);
                visibility: visible;
            }

            /* Overlay belakang sidebar */
            .admin-sidebar-overlay {
                display: block;
                position: fixed;
                inset: 0;

                z-index: 10000;

                background: rgba(15, 37, 27, .48);

                opacity: 0;
                visibility: hidden;

                transition:
                    opacity .3s ease,
                    visibility .3s ease;
            }

            .admin-sidebar-overlay.is-active {
                opacity: 1;
                visibility: visible;
            }

            /* Main content */
            .admin-shell {
                width: 100%;
                min-width: 0;
                overflow-x: hidden;
            }

            .admin-main {
                width: 100%;
                min-width: 0;
                margin-left: 0 !important;
            }

            /* Topbar */
            .admin-topbar {
                gap: 12px;
                padding: 14px 16px;
            }

            .admin-topbar-left {
                gap: 10px;
            }

            .admin-topbar h1 {
                font-size: 18px;
            }

            .admin-profile {
                flex-shrink: 0;
            }

            /* Kunci scroll saat menu terbuka */
            body.sidebar-open {
                overflow: hidden;
            }
        }

        /* =====================================
           MOBILE KECIL
        ===================================== */

        @media (max-width: 480px) {

            .admin-menu-toggle {
                width: 38px;
                height: 38px;
                font-size: 23px;
            }

            .admin-topbar-left {
                gap: 9px;
            }

            .admin-topbar h1 {
                font-size: 16px;
            }

            .admin-profile small {
                display: none;
            }

            .admin-profile strong {
                font-size: 12px;
            }
        }
    </style>
</head>

<body class="admin-body">

    <div class="admin-shell">

        <!-- =====================================
             SIDEBAR
        ====================================== -->

        <aside class="admin-sidebar" id="adminSidebar">

            <!-- BRAND -->
            <a href="<?= site_url('admin') ?>" class="admin-brand">

                <span class="admin-brand__icon">
                    <i class="bi bi-journal-richtext"></i>
                </span>

                <span>
                    <strong>Usada Bali</strong>
                    <small>Administration</small>
                </span>

            </a>

            <!-- LABEL MENU -->
            <div class="admin-sidebar__label">
                MENU UTAMA
            </div>

            <!-- NAVIGATION -->
            <nav class="admin-nav">

                <!-- DASHBOARD -->
                <a
                    href="<?= site_url('admin') ?>"
                    class="admin-nav__link <?= ($active ?? '') === 'dashboard' ? 'is-active' : '' ?>"
                >
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>

                <!-- GLOSARIUM -->
                <a
                    href="<?= site_url('admin/glosarium') ?>"
                    class="admin-nav__link <?= ($active ?? '') === 'glosarium' ? 'is-active' : '' ?>"
                >
                    <i class="bi bi-book-half"></i>
                    Glosarium
                </a>

                <!-- DATA USADA -->
                <a
                    href="<?= site_url('admin/entri-usada') ?>"
                    class="admin-nav__link <?= ($active ?? '') === 'entri-usada' ? 'is-active' : '' ?>"
                >
                    <i class="bi bi-journal-text"></i>
                    Data Usada
                </a>

                <!-- PENGOBATAN -->
                <a
                    href="<?= site_url('admin/pengobatan') ?>"
                    class="admin-nav__link <?= ($active ?? '') === 'pengobatan' ? 'is-active' : '' ?>"
                >
                    <i class="bi bi-capsule"></i>
                    Pengobatan
                </a>

            </nav>

            <!-- =====================================
                 SIDEBAR BOTTOM
            ====================================== -->

            <div class="admin-sidebar__bottom">

                <!-- LIHAT WEBSITE -->
                <a
                    href="<?= base_url('/') ?>"
                    target="_blank"
                    rel="noopener"
                    class="admin-nav__link"
                >
                    <i class="bi bi-box-arrow-up-right"></i>
                    Lihat Website
                </a>

                <!-- LOGOUT -->
                <form action="<?= site_url('admin/logout') ?>" method="post">

                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="admin-nav__link admin-logout"
                    >
                        <i class="bi bi-box-arrow-left"></i>
                        Keluar
                    </button>

                </form>

            </div>

        </aside>

        <!-- SIDEBAR OVERLAY -->
        <div
            class="admin-sidebar-overlay"
            id="adminSidebarOverlay"
        ></div>


        <!-- =====================================
             MAIN CONTENT
        ====================================== -->

        <main class="admin-main">

            <!-- TOPBAR -->
            <header class="admin-topbar">

                <div class="admin-topbar-left">

                    <!-- HAMBURGER BUTTON -->
                    <button
                        type="button"
                        class="admin-menu-toggle"
                        id="adminMenuToggle"
                        aria-label="Buka menu navigasi"
                        aria-expanded="false"
                        aria-controls="adminSidebar"
                    >
                        <i class="bi bi-list"></i>
                    </button>

                    <!-- TITLE -->
                    <div>
                        <span class="admin-topbar__eyebrow">
                            USADA BALI
                        </span>

                        <h1>Panel Administrasi</h1>
                    </div>

                </div>

                <!-- PROFILE -->
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


            <!-- =====================================
                 PAGE CONTENT
            ====================================== -->

            <section class="admin-content">

                <?= $this->renderSection('content') ?>

            </section>

        </main>

    </div>


    <!-- =====================================
         HAMBURGER MENU JAVASCRIPT
    ====================================== -->

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const toggle = document.getElementById('adminMenuToggle');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('adminSidebarOverlay');

        if (!toggle || !sidebar || !overlay) return;

        function openSidebar() {

            sidebar.classList.add('is-open');
            overlay.classList.add('is-active');

            document.body.classList.add('sidebar-open');

            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'Tutup menu navigasi');

            toggle.innerHTML = '<i class="bi bi-x-lg"></i>';
        }

        function closeSidebar() {

            sidebar.classList.remove('is-open');
            overlay.classList.remove('is-active');

            document.body.classList.remove('sidebar-open');

            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Buka menu navigasi');

            toggle.innerHTML = '<i class="bi bi-list"></i>';
        }

        /* KLIK HAMBURGER */
        toggle.addEventListener('click', function () {

            const isOpen = sidebar.classList.contains('is-open');

            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }

        });

        /* KLIK OVERLAY */
        overlay.addEventListener('click', closeSidebar);

        /* TOMBOL ESCAPE */
        document.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {
                closeSidebar();
            }

        });

        /* KLIK MENU NAVIGASI */
        sidebar.querySelectorAll('.admin-nav__link').forEach(function (link) {

            link.addEventListener('click', function () {

                if (window.innerWidth <= 768) {
                    closeSidebar();
                }

            });

        });

        /* KEMBALI KE DESKTOP */
        window.addEventListener('resize', function () {

            if (window.innerWidth > 768) {
                closeSidebar();
            }

        });

    });
    </script>

</body>
</html>