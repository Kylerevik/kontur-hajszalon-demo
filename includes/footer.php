</main>
<?php
$scripts ??= [];
$showMobileCta ??= true;
?>
<footer class="site-footer<?= $showMobileCta ? ' site-footer--with-cta' : '' ?>" id="contact">
    <div class="container">
        <div class="footer-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <a class="navbar-brand brand" href="index.php">
                    <img src="<?= e(asset('img/logo.svg')) ?>" alt="" width="40" height="40">
                    <span class="brand__name">Kontúr <small>Hajszalon</small></span>
                </a>
                <p class="footer-tagline">Egyszemélyes belvárosi szalon, ahol minden vendég saját időpontot kap.</p>
            </div>
            <a class="btn btn-accent d-none d-md-inline-flex" href="booking.php">Időpontot foglalok</a>
        </div>
        <div class="row gy-4 py-5">
            <div class="col-md-4">
                <p class="footer-title">Cím</p>
                <address class="mb-0">
                    <?= e(SALON_STREET) ?><br>
                    <?= e(SALON_POSTAL_CODE) ?> <?= e(SALON_CITY) ?>
                </address>
            </div>
            <div class="col-md-4">
                <p class="footer-title">Nyitvatartás</p>
                <?php require __DIR__ . '/hours_list.php'; ?>
            </div>
            <div class="col-md-4">
                <p class="footer-title">Kapcsolat</p>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= e(phone_link(SALON_PHONE)) ?>"><?= e(SALON_PHONE) ?></a></li>
                    <li><a href="mailto:<?= e(SALON_EMAIL) ?>"><?= e(SALON_EMAIL) ?></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-column flex-sm-row flex-sm-wrap justify-content-between gap-2 py-3">
            <span>&copy; <?= date('Y') ?> Kontúr Hajszalon</span>
            <span>Készítette: <span class="developer" tabindex="0" aria-describedby="developer-name"><?= e(DEVELOPER_ALIAS) ?><span class="developer__bubble" id="developer-name" role="tooltip"><?= e(DEVELOPER_NAME) ?></span></span>, webfejlesztő</span>
            <a href="admin/login.php">Admin belépés</a>
        </div>
    </div>
</footer>
<?php if ($showMobileCta): ?>
<div class="mobile-cta d-md-none">
    <a class="btn btn-accent" href="booking.php">Foglalás</a>
    <a class="btn btn-outline-light" href="<?= e(phone_link(SALON_PHONE)) ?>">Hívás</a>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<?php foreach ($scripts as $script): ?>
<script src="<?= e(asset($script)) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
