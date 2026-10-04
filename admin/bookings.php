<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require dirname(__DIR__) . '/includes/admin_bookings.php';

require_admin();

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$type, $message] = handle_booking_action($db, $_POST);
    flash($type, $message);
    redirect($_SERVER['REQUEST_URI']);
}

$today = new DateTimeImmutable('today');
$filters = read_booking_filters($_GET);
$bookings = fetch_admin_bookings($db, $filters, $today->format('Y-m-d'));
$stats = fetch_booking_stats($db, $today);
$flash = take_flash();

$bookingsByDate = [];
foreach ($bookings as $booking) {
    $bookingsByDate[$booking['booking_date']][] = $booking;
}

$pageTitle = 'Foglalások – Kontúr admin';
$scripts = ['js/admin.js'];

require dirname(__DIR__) . '/includes/admin_header.php';
?>
<h1 class="admin-title">Foglalások</h1>

<?php if ($flash !== null): ?>
<div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="stat">
            <span class="stat__value"><?= $stats['today_count'] ?></span>
            <span class="stat__label">Ma</span>
        </div>
    </div>
    <div class="col-4">
        <div class="stat">
            <span class="stat__value"><?= $stats['week_count'] ?></span>
            <span class="stat__label">A következő 7 napban</span>
        </div>
    </div>
    <div class="col-4">
        <div class="stat">
            <span class="stat__value"><?= $stats['upcoming_count'] ?></span>
            <span class="stat__label">Közelgő összesen</span>
        </div>
    </div>
</div>

<form class="filter-bar" method="get" action="bookings.php">
    <div>
        <label class="form-label" for="filter-view">Nézet</label>
        <select class="form-select" id="filter-view" name="view">
<?php foreach (ADMIN_VIEWS as $value => $label): ?>
            <option value="<?= e($value) ?>"<?= $filters['view'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="form-label" for="filter-date">Egy adott nap</label>
        <input class="form-control" type="date" id="filter-date" name="date" value="<?= e($filters['date']) ?>">
    </div>
    <div>
        <label class="form-label" for="filter-status">Állapot</label>
        <select class="form-select" id="filter-status" name="status">
            <option value="">Mind</option>
<?php foreach (BOOKING_STATUSES as $value => $label): ?>
            <option value="<?= e($value) ?>"<?= $filters['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
        </select>
    </div>
    <div class="filter-bar__buttons">
        <button class="btn btn-ink" type="submit">Szűrés</button>
        <a class="btn btn-outline-ink" href="bookings.php">Alaphelyzet</a>
    </div>
</form>

<?php if ($bookingsByDate === []): ?>
<p class="empty-state">Nincs a szűrésnek megfelelő foglalás.</p>
<?php endif; ?>

<?php foreach ($bookingsByDate as $date => $dayBookings): ?>
<section class="day-group">
    <h2 class="day-group__title"><?= e(format_date_hu($date)) ?> <span><?= count($dayBookings) ?> foglalás</span></h2>
<?php foreach ($dayBookings as $booking): ?>
    <article class="booking-card booking-card--<?= e($booking['status']) ?>">
        <div class="booking-card__time"><?= e(format_time($booking['start_time'])) ?> – <?= e(format_time($booking['end_time'])) ?></div>
        <div class="booking-card__main">
            <h3 class="booking-card__service"><?= e($booking['service_name']) ?> <span><?= e(format_price((int) $booking['price'])) ?></span></h3>
            <p class="booking-card__customer"><?= e($booking['customer_name']) ?> &middot; <a href="<?= e(phone_link($booking['customer_phone'])) ?>"><?= e($booking['customer_phone']) ?></a></p>
            <p class="booking-card__meta">Azonosító: <?= e($booking['code']) ?> &middot; Foglalva: <?= e((new DateTimeImmutable($booking['created_at']))->format('Y.m.d. H:i')) ?></p>
        </div>
        <div class="booking-card__side">
            <span class="status-badge status-badge--<?= e($booking['status']) ?>"><?= e(BOOKING_STATUSES[$booking['status']]) ?></span>
            <div class="booking-card__actions">
                <form class="d-flex gap-2" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                    <input type="hidden" name="action" value="status">
                    <select class="form-select form-select-sm" name="status" aria-label="Állapot: <?= e($booking['customer_name']) ?>">
<?php foreach (BOOKING_STATUSES as $value => $label): ?>
                        <option value="<?= e($value) ?>"<?= $booking['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-outline-ink" type="submit">Mentés</button>
                </form>
                <form method="post" data-confirm="Biztosan törlöd <?= e($booking['customer_name']) ?> foglalását? A művelet nem vonható vissza.">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Törlés</button>
                </form>
            </div>
        </div>
    </article>
<?php endforeach; ?>
</section>
<?php endforeach; ?>
<?php require dirname(__DIR__) . '/includes/admin_footer.php'; ?>
