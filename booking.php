<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Időpontfoglalás – Kontúr Hajszalon, Pécs';
$pageDescription = 'Foglalj időpontot online a Kontúr Hajszalonba: válaszd ki a szolgáltatást, a napot és a szabad időpontot.';
$activePage = '';
$scripts = ['js/booking.js'];

$values = [
    'service_id' => (string) ($_GET['service'] ?? ''),
    'booking_date' => '',
    'start_time' => '',
    'customer_name' => '',
    'customer_phone' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = validate_booking_input($_POST);
    $values = $result['values'];
    $errors = $result['errors'];

    if (!csrf_is_valid()) {
        $errors['form'] = 'Az űrlap érvényessége lejárt. Kérjük, küldd el újra.';
    }

    if (!$errors) {
        $code = null;
        try {
            $code = create_booking(
                db(),
                $result['service'],
                $result['date'],
                $values['start_time'],
                $values['customer_name'],
                $values['customer_phone']
            );
        } catch (PDOException | RuntimeException $exception) {
            error_log('Foglalási hiba: ' . $exception->getMessage());
            $errors['form'] = 'Technikai hiba történt. Kérjük, próbáld újra, vagy hívj minket telefonon.';
        }

        if ($code !== null) {
            redirect('confirmation.php?code=' . $code);
        }
        if (!$errors) {
            $errors['start_time'] = 'Ezt az időpontot közben valaki lefoglalta. Válassz egy másikat.';
        }
    }
}

$servicesByCategory = fetch_services_by_category();
$today = new DateTimeImmutable('today');
$minDate = $today->format('Y-m-d');
$maxDate = $today->modify('+' . BOOKING_WINDOW_DAYS . ' days')->format('Y-m-d');

require __DIR__ . '/includes/header.php';
?>
<section class="page-intro">
    <div class="container">
        <h1 class="page-intro__title">Időpontfoglalás</h1>
        <p class="page-intro__lead">Három lépés: válaszd ki a szolgáltatást, a napot és a számodra megfelelő időpontot, majd add meg az elérhetőségedet. A foglalásodat azonnal látod az oldalon.</p>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-xl-8">
                <form id="booking-form" method="post" action="booking.php" data-slots-url="api/slots.php">
                    <?= csrf_field() ?>
                    <div class="hp-field" aria-hidden="true">
                        <label for="website">Ezt a mezőt hagyd üresen</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

<?php if (isset($errors['form'])): ?>
                    <div class="alert alert-danger" role="alert"><?= e($errors['form']) ?></div>
<?php endif; ?>

                    <fieldset class="booking-step">
                        <legend class="booking-step__title"><span class="steps__number">1</span> Szolgáltatás</legend>
<?php foreach ($servicesByCategory as $category => $services): ?>
                        <p class="booking-step__group"><?= e($category) ?></p>
                        <div class="service-options">
<?php foreach ($services as $service): ?>
                            <label class="service-option">
                                <input class="service-option__input" type="radio" name="service_id" value="<?= (int) $service['id'] ?>" required<?= (string) $service['id'] === $values['service_id'] ? ' checked' : '' ?>>
                                <span class="service-option__body">
                                    <span class="service-option__name"><?= e($service['name']) ?></span>
                                    <span class="service-option__meta"><?= e(format_duration((int) $service['duration_minutes'])) ?> · <?= e(format_price((int) $service['price'])) ?></span>
                                </span>
                            </label>
<?php endforeach; ?>
                        </div>
<?php endforeach; ?>
                        <?= field_error($errors, 'service_id') ?>
                    </fieldset>

                    <fieldset class="booking-step">
                        <legend class="booking-step__title"><span class="steps__number">2</span> Dátum és időpont</legend>
                        <div class="mb-3">
                            <label class="form-label" for="booking-date">Nap</label>
                            <input class="form-control booking-date<?= isset($errors['booking_date']) ? ' is-invalid' : '' ?>" type="date" id="booking-date" name="booking_date" value="<?= e($values['booking_date']) ?>" min="<?= e($minDate) ?>" max="<?= e($maxDate) ?>" required>
                            <?= field_error($errors, 'booking_date') ?>
                        </div>
                        <p id="slot-message" class="slot-message" aria-live="polite">Válassz szolgáltatást és napot, hogy lásd a szabad időpontokat.</p>
                        <div id="slot-list" class="slot-list" role="radiogroup" aria-label="Szabad kezdési időpontok" data-selected="<?= e($values['start_time']) ?>"></div>
                        <?= field_error($errors, 'start_time') ?>
                        <noscript><p class="alert alert-warning mb-0">Az időpontok betöltéséhez kapcsold be a JavaScriptet, vagy hívj minket: <a href="<?= e(phone_link(SALON_PHONE)) ?>"><?= e(SALON_PHONE) ?></a>.</p></noscript>
                    </fieldset>

                    <fieldset class="booking-step">
                        <legend class="booking-step__title"><span class="steps__number">3</span> Elérhetőség</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="customer-name">Teljes neved</label>
                                <input class="form-control<?= isset($errors['customer_name']) ? ' is-invalid' : '' ?>" type="text" id="customer-name" name="customer_name" value="<?= e($values['customer_name']) ?>" minlength="2" maxlength="80" autocomplete="name" required>
                                <?= field_error($errors, 'customer_name') ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="customer-phone">Telefonszám</label>
                                <input class="form-control<?= isset($errors['customer_phone']) ? ' is-invalid' : '' ?>" type="tel" id="customer-phone" name="customer_phone" value="<?= e($values['customer_phone']) ?>" maxlength="20" autocomplete="tel" inputmode="tel" required>
                                <div class="form-text">Ezen a számon keresünk, ha változás van.</div>
                                <?= field_error($errors, 'customer_phone') ?>
                            </div>
                        </div>
                    </fieldset>

                    <button class="btn btn-accent btn-lg" type="submit">Foglalás elküldése</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
