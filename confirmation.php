<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

allow_methods(['GET']);

$code = input_string($_GET, 'code');
$booking = preg_match('/^[A-Z0-9]{8}$/', $code) ? find_booking_by_code($code) : null;

if ($booking === null) {
    http_response_code(404);
}

$pageTitle = ($booking === null ? 'A foglalás nem található' : 'Foglalásodat rögzítettük') . ' – Kontúr Hajszalon';
$activePage = '';
$headExtra = '<meta name="robots" content="noindex">';

require __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <div class="confirmation">
<?php if ($booking === null): ?>
            <h1 class="page-intro__title">A foglalás nem található</h1>
            <p>Ellenőrizd a foglalási azonosítót, vagy foglalj új időpontot.</p>
            <a class="btn btn-accent" href="booking.php">Új foglalás</a>
<?php else: ?>
            <svg class="confirmation__icon" viewBox="0 0 48 48" width="56" height="56" fill="none" aria-hidden="true">
                <circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="2.5"/>
                <path d="M14 25l7 7 13-15" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1 class="page-intro__title">Foglalásodat rögzítettük</h1>
            <p class="page-intro__lead">Köszönjük, <?= e($booking['customer_name']) ?>! Várunk a szalonban.</p>

            <dl class="confirmation__details">
                <dt>Szolgáltatás</dt>
                <dd><?= e($booking['service_name']) ?></dd>
                <dt>Időpont</dt>
                <dd><?= e(format_date_hu($booking['booking_date'])) ?>,<br><?= e(format_time($booking['start_time'])) ?> – <?= e(format_time($booking['end_time'])) ?></dd>
                <dt>Ár</dt>
                <dd><?= e(format_price((int) $booking['price'])) ?>, fizetés a helyszínen</dd>
                <dt>Állapot</dt>
                <dd><?= e(BOOKING_STATUSES[$booking['status']]) ?></dd>
                <dt>Foglalási azonosító</dt>
                <dd class="confirmation__code"><?= e($booking['code']) ?></dd>
            </dl>

            <p>Kérjük, érkezz pár perccel korábban. Ha mégsem tudsz eljönni, jelezd telefonon: <a href="<?= e(phone_link(SALON_PHONE)) ?>"><?= e(SALON_PHONE) ?></a>.</p>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-accent" href="index.php">Vissza a főoldalra</a>
                <a class="btn btn-outline-ink" href="booking.php">Újabb foglalás</a>
            </div>
<?php endif; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
