<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

allow_methods(['GET']);

$pageTitle = 'Kontúr Hajszalon, Pécs – hajvágás, festés, szakálligazítás';
$pageDescription = 'Hajvágás, festés, melír és szakálligazítás Pécs belvárosában. Foglalj időpontot online, a szolgáltatás idejére szabva.';
$activePage = 'home';

$categories = fetch_category_summaries();
$openingHours = fetch_opening_hours();

$categoryNotes = [
    'Női' => 'Hajvágás, pontvágás és alkalmi frizurák.',
    'Férfi' => 'Hajvágás, szakálligazítás borotvával.',
    'Gyerek' => 'Türelmes hajvágás 12 éves korig.',
    'Festés és melír' => 'Tőfestés, melír és kézzel festett balayage.',
    'Ápolás' => 'Mélytáplálás sérült vagy festett hajra.',
];

$schemaDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$schemaHours = [];
foreach ($openingHours as $weekday => $hours) {
    $schemaHours[] = [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => $schemaDays[$weekday - 1],
        'opens' => format_time($hours['open_time']),
        'closes' => format_time($hours['close_time']),
    ];
}
$headExtra = '<script type="application/ld+json">' . json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'HairSalon',
    'name' => SALON_NAME,
    'telephone' => SALON_PHONE,
    'email' => SALON_EMAIL,
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => SALON_STREET,
        'postalCode' => SALON_POSTAL_CODE,
        'addressLocality' => SALON_CITY,
        'addressCountry' => 'HU',
    ],
    'openingHoursSpecification' => $schemaHours,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . '</script>';

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container">
        <div class="row align-items-end gy-4">
            <div class="col-lg-7 pb-lg-5">
                <p class="eyebrow"><?= e(SALON_CITY) ?>, <?= e(SALON_STREET) ?></p>
                <h1 class="hero__title">Frizura, ami a te napodhoz igazodik.</h1>
                <p class="hero__lead">Egy szék, egy vendég egyszerre. Nálunk nincs sorban állás és nincs rohanás: a hajvágásra, festésre vagy szakálligazításra pontosan annyi időt szánunk, amennyi kell.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-accent btn-lg" href="booking.php">Időpontot foglalok</a>
                    <a class="btn btn-outline-light btn-lg" href="services.php">Szolgáltatások és árak</a>
                </div>
            </div>
            <div class="col-lg-5 text-center text-lg-end">
                <img class="hero__art" src="<?= e(asset('img/hero.svg')) ?>" alt="" width="520" height="620">
            </div>
        </div>
    </div>
</section>

<section class="section section--sand">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4">
                <h2 class="section__title">Kis szalon, teljes figyelem</h2>
                <p>A Kontúr a pécsi belvárosban működő egyszemélyes szalon. <?= e(SALON_OWNER) ?> minden vendéget maga fogad, a munka elejétől a végéig.</p>
            </div>
            <div class="col-lg-8">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="point">
                            <h3 class="point__title">Egyszerre egy vendég</h3>
                            <p class="mb-0">Nem kell várnod, és senki sem siet veled. Az időpontod csak a tiéd.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="point">
                            <h3 class="point__title">Beszélgetéssel kezdünk</h3>
                            <p class="mb-0">Vágás vagy festés előtt átbeszéljük, mit szeretnél, és mi illik a hajadhoz és a mindennapjaidhoz.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="point">
                            <h3 class="point__title">Előre tudod az árat</h3>
                            <p class="mb-0">Minden szolgáltatásnál látod, mennyibe kerül és meddig tart, így a napodat is be tudod osztani.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
            <h2 class="section__title mb-0">Amiben segíthetünk</h2>
            <a class="link-accent" href="services.php">Minden szolgáltatás árakkal</a>
        </div>
        <div class="row g-3">
<?php foreach ($categories as $category): ?>
            <div class="col-sm-6 col-lg-4">
                <a class="category-card" href="services.php">
                    <span class="category-card__name"><?= e($category['category']) ?></span>
                    <span class="category-card__note"><?= e($categoryNotes[$category['category']] ?? '') ?></span>
                    <span class="category-card__price">már <?= e(format_price((int) $category['min_price'])) ?>-tól</span>
                </a>
            </div>
<?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--sand">
    <div class="container">
        <h2 class="section__title">Így foglalhatsz</h2>
        <ol class="steps">
            <li>
                <span class="steps__number">1</span>
                <h3 class="point__title">Válassz szolgáltatást</h3>
                <p class="mb-0">Megmutatjuk az árát és az időtartamát is.</p>
            </li>
            <li>
                <span class="steps__number">2</span>
                <h3 class="point__title">Válassz napot és időpontot</h3>
                <p class="mb-0">Csak azokat a kezdési időpontokat kínáljuk fel, amelyekbe a munka beleférhet.</p>
            </li>
            <li>
                <span class="steps__number">3</span>
                <h3 class="point__title">Add meg a neved és a telefonszámod</h3>
                <p class="mb-0">A foglalásodat azonnal látod az oldalon, ha pedig változik a terved, hívj minket.</p>
            </li>
        </ol>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-band d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h2 class="cta-band__title mb-0">Keresel egy szabad időpontot?</h2>
            <a class="btn btn-accent btn-lg" href="booking.php">Időpontfoglalás</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
