<?= $this->extend('admin/layouts/auth') ?>

<?= $this->section('content') ?>

<div class="login-card">

    <a href="<?= base_url('/') ?>" class="login-brand">
        <span class="login-brand__icon">
            <i class="bi bi-journal-richtext"></i>
        </span>

        <span>
            <strong>Usada Bali</strong>
            <small>Admin Panel</small>
        </span>
    </a>

    <div class="login-heading">
        <span class="login-eyebrow">ADMINISTRATION</span>
        <h1>Selamat Datang</h1>
        <p>Silakan masuk untuk mengelola arsip digital Usada Bali.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="login-alert login-alert--error">
            <i class="bi bi-exclamation-circle"></i>
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="login-alert login-alert--success">
            <i class="bi bi-check-circle"></i>
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('admin/login') ?>" method="post">

        <?= csrf_field() ?>

        <div class="login-field">
            <label for="username">Username</label>

            <div class="login-input">
                <i class="bi bi-person"></i>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    value="<?= esc(old('username') ?? '', 'attr') ?>"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>
        </div>

        <div class="login-field">
            <label for="password">Password</label>

            <div class="login-input">
                <i class="bi bi-lock"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >
            </div>
        </div>

        <button type="submit" class="login-submit">
            Masuk ke Dashboard
            <i class="bi bi-arrow-right"></i>
        </button>

    </form>

    <a href="<?= base_url('/') ?>" class="login-back">
        <i class="bi bi-arrow-left"></i>
        Kembali ke Website
    </a>

</div>

<?= $this->endSection() ?>
