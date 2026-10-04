<?php
$assetPrefix = '../';
$stylesheets = ['css/style.css', 'css/admin.css'];
$headExtra = '<meta name="robots" content="noindex, nofollow">';
require __DIR__ . '/head.php';
?>
<header class="admin-bar">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2 py-2">
        <a class="brand" href="bookings.php">
            <img src="<?= e(asset('img/logo.svg', '../')) ?>" alt="" width="32" height="32">
            <span class="brand__name">Kontúr <small>Admin</small></span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a class="admin-bar__link" href="../index.php">Weboldal</a>
<?php if (is_admin()): ?>
            <form method="post" action="logout.php">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline-light" type="submit">Kilépés</button>
            </form>
<?php endif; ?>
        </div>
    </div>
</header>
<main class="flex-grow-1 admin-main">
    <div class="container">
