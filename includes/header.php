<?php
require __DIR__ . '/head.php';

$navItems = [
    'home' => ['index.php', 'Főoldal'],
    'services' => ['services.php', 'Szolgáltatások és árak'],
    'contact' => ['#contact', 'Elérhetőség'],
];
$activePage ??= '';
?>
<a class="visually-hidden-focusable skip-link" href="#main">Ugrás a tartalomra</a>
<header class="site-header">
    <nav class="navbar navbar-expand-md" aria-label="Fő navigáció">
        <div class="container">
            <a class="navbar-brand brand" href="index.php">
                <img src="<?= e(asset('img/logo.svg')) ?>" alt="" width="40" height="40">
                <span class="brand__name">Kontúr <small>Hajszalon</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Menü megnyitása">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="main-nav">
                <ul class="navbar-nav ms-md-auto align-items-md-center gap-md-2">
<?php foreach ($navItems as $key => [$href, $label]): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $activePage === $key ? ' active' : '' ?>" href="<?= e($href) ?>"<?= $activePage === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
                    </li>
<?php endforeach; ?>
                    <li class="nav-item ms-md-2 mt-2 mt-md-0">
                        <a class="btn btn-accent" href="booking.php">Időpontfoglalás</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main id="main" class="flex-grow-1">
