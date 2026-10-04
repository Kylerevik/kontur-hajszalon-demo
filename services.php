<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Szolgáltatások és árak – Kontúr Hajszalon, Pécs';
$pageDescription = 'Női és férfi hajvágás, hajfestés, melír, balayage és szakálligazítás árakkal és időtartammal. Foglalj online a Kontúr Hajszalonba Pécsett.';
$activePage = 'services';

$servicesByCategory = fetch_services_by_category();

require __DIR__ . '/includes/header.php';
?>
<section class="page-intro">
    <div class="container">
        <h1 class="page-intro__title">Szolgáltatások és árak</h1>
        <p class="page-intro__lead">Az árak fixek, az időtartam pedig az a sáv, amit a foglalásnál lefoglalunk neked. A festés és a melír ára a hajhossztól függően kissé változhat, ezt a konzultáción egyeztetjük.</p>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
<?php foreach ($servicesByCategory as $category => $services): ?>
        <h2 class="category-title"><?= e($category) ?></h2>
        <ul class="service-list">
<?php foreach ($services as $service): ?>
            <li class="service-row">
                <div class="service-row__info">
                    <h3 class="service-row__name"><?= e($service['name']) ?></h3>
                    <p class="service-row__description"><?= e($service['description']) ?></p>
                </div>
                <div class="service-row__meta">
                    <span class="service-row__duration"><?= e(format_duration((int) $service['duration_minutes'])) ?></span>
                    <span class="service-row__price"><?= e(format_price((int) $service['price'])) ?></span>
                    <a class="btn btn-outline-ink btn-sm" href="booking.php?service=<?= (int) $service['id'] ?>">Foglalás<span class="visually-hidden">: <?= e($service['name']) ?></span></a>
                </div>
            </li>
<?php endforeach; ?>
        </ul>
<?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
