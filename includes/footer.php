</main>
<?php
$openingHours ??= fetch_opening_hours();
$scripts ??= [];
?>
<footer class="site-footer" id="contact">
    <div class="container">
        <div class="row gy-4 py-5">
            <div class="col-md-4">
                <p class="footer-title">Kontúr Hajszalon</p>
                <address class="mb-0">
                    <?= e(SALON_STREET) ?><br>
                    <?= e(SALON_POSTAL_CODE) ?> <?= e(SALON_CITY) ?>
                </address>
            </div>
            <div class="col-md-4">
                <p class="footer-title">Nyitvatartás</p>
                <dl class="hours-list mb-0">
<?php for ($weekday = 1; $weekday <= 7; $weekday++): ?>
                    <dt><?= e(ucfirst(HU_WEEKDAYS[$weekday - 1])) ?></dt>
                    <dd><?= isset($openingHours[$weekday])
                        ? e(format_time($openingHours[$weekday]['open_time']) . ' – ' . format_time($openingHours[$weekday]['close_time']))
                        : 'Zárva' ?></dd>
<?php endfor; ?>
                </dl>
            </div>
            <div class="col-md-4">
                <p class="footer-title">Kapcsolat</p>
                <ul class="list-unstyled mb-3">
                    <li><a href="<?= e(phone_link(SALON_PHONE)) ?>"><?= e(SALON_PHONE) ?></a></li>
                    <li><a href="mailto:<?= e(SALON_EMAIL) ?>"><?= e(SALON_EMAIL) ?></a></li>
                </ul>
                <a class="btn btn-accent" href="booking.php">Időpontot foglalok</a>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-column flex-sm-row flex-sm-wrap justify-content-between gap-2 py-3">
            <span>&copy; <?= date('Y') ?> Kontúr Hajszalon</span>
            <span>Készítette: <span class="developer" tabindex="0" aria-describedby="developer-name"><?= e(DEVELOPER_ALIAS) ?><span class="developer__bubble" id="developer-name" role="tooltip"><?= e(DEVELOPER_NAME) ?></span></span>, webfejlesztő</span>
            <a href="admin/login.php">Admin belépés</a>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<?php foreach ($scripts as $script): ?>
<script src="<?= e(asset($script)) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
