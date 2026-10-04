<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
allow_methods(['GET'], true);

function describe_slots(PDO $db, DateTimeImmutable $date, array $service, array $slots): string
{
    if ($slots !== []) {
        return 'Szabad kezdési időpontok (a munka kb. ' . format_duration((int) $service['duration_minutes']) . '):';
    }
    if (!is_bookable_date($date)) {
        return 'Erre a napra nem lehet időpontot foglalni.';
    }
    if (fetch_day_hours($db, $date) === null) {
        return 'Ezen a napon zárva tartunk. Válassz egy másik napot.';
    }

    return 'Erre a napra már nincs szabad időpont ennél a szolgáltatásnál. Próbálj másik napot.';
}

$serviceId = $_GET['service'] ?? '';
$dateValue = $_GET['date'] ?? '';
$service = is_string($serviceId) && ctype_digit($serviceId) ? find_service((int) $serviceId) : null;
$date = is_string($dateValue) ? parse_date($dateValue) : null;

if ($service === null || $date === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Érvénytelen kérés.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$db = db();
$slots = available_slots($db, $date, $service);

echo json_encode([
    'slots' => $slots,
    'message' => describe_slots($db, $date, $service, $slots),
], JSON_UNESCAPED_UNICODE);
