<?php
declare(strict_types=1);

const SALON_NAME = 'Kontúr Hajszalon';
const SALON_OWNER = 'Kovács Nóra';
const SALON_PHONE = '+36 72 555 0142';
const SALON_EMAIL = 'hello@kontur-hajszalon.example';
const SALON_STREET = 'Király utca 41.';
const SALON_CITY = 'Pécs';
const SALON_POSTAL_CODE = '7621';
const DEVELOPER_ALIAS = 'chill';
const DEVELOPER_NAME = 'Illés Gergely';

date_default_timezone_set('Europe/Budapest');
ini_set('display_errors', '0');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

require __DIR__ . '/helpers.php';
require __DIR__ . '/database.php';
require __DIR__ . '/catalog.php';
require __DIR__ . '/scheduling.php';
require __DIR__ . '/booking_logic.php';
