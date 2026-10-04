<?php
declare(strict_types=1);

const BOOKING_CODE_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

/**
 * A foglalási űrlap ellenőrzése.
 * Visszaadja a tisztított értékeket, a hibákat (mezőnként), valamint a feloldott szolgáltatást és dátumot.
 */
function validate_booking_input(array $post): array
{
    $values = [
        'service_id' => trim((string) ($post['service_id'] ?? '')),
        'booking_date' => trim((string) ($post['booking_date'] ?? '')),
        'start_time' => trim((string) ($post['start_time'] ?? '')),
        'customer_name' => trim((string) ($post['customer_name'] ?? '')),
        'customer_phone' => trim((string) ($post['customer_phone'] ?? '')),
    ];
    $errors = [];

    // A rejtett mezőt csak robotok töltik ki.
    if (trim((string) ($post['website'] ?? '')) !== '') {
        $errors['form'] = 'A foglalást nem sikerült rögzíteni. Kérjük, próbáld újra.';
    }

    $service = ctype_digit($values['service_id']) ? find_service((int) $values['service_id']) : null;
    if ($service === null) {
        $errors['service_id'] = 'Válassz szolgáltatást.';
    }

    $date = parse_date($values['booking_date']);
    if ($date === null) {
        $errors['booking_date'] = 'Válassz érvényes dátumot.';
    } elseif (!is_bookable_date($date)) {
        $errors['booking_date'] = 'Erre a napra nem lehet időpontot foglalni.';
    }

    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $values['start_time'])) {
        $errors['start_time'] = 'Válassz időpontot a felkínált lehetőségek közül.';
    }

    $nameLength = mb_strlen($values['customer_name']);
    if ($nameLength < 2 || $nameLength > 80 || !preg_match('/^[\p{L}\p{M}][\p{L}\p{M}\s.\'’-]*$/u', $values['customer_name'])) {
        $errors['customer_name'] = 'Add meg a teljes neved (csak betűk, legfeljebb 80 karakter).';
    }

    $phoneDigits = preg_replace('/\D/', '', $values['customer_phone']);
    if (
        !preg_match('/^\+?\d[\d\s\/()-]{5,19}$/', $values['customer_phone'])
        || strlen($phoneDigits) < 9
        || strlen($phoneDigits) > 15
    ) {
        $errors['customer_phone'] = 'Adj meg érvényes telefonszámot, például +36 30 123 4567.';
    }

    return ['values' => $values, 'errors' => $errors, 'service' => $service, 'date' => $date];
}

function generate_booking_code(): string
{
    $code = '';
    $max = strlen(BOOKING_CODE_ALPHABET) - 1;

    for ($i = 0; $i < 8; $i++) {
        $code .= BOOKING_CODE_ALPHABET[random_int(0, $max)];
    }

    return $code;
}

/**
 * Rögzíti a foglalást, ha az időpont még szabad. Foglalt időpont esetén null a visszatérési érték.
 * A napra szóló névzár miatt két egyidejű kérés nem foglalhatja le ugyanazt a sávot.
 */
function create_booking(PDO $db, array $service, DateTimeImmutable $date, string $time, string $name, string $phone): ?string
{
    $lockName = 'booking_' . $date->format('Y-m-d');
    $acquire = $db->prepare('SELECT GET_LOCK(?, 5)');
    $acquire->execute([$lockName]);

    if ((int) $acquire->fetchColumn() !== 1) {
        throw new RuntimeException('A foglalási zár nem szerezhető meg.');
    }

    try {
        if (!in_array($time, available_slots($db, $date, $service), true)) {
            return null;
        }

        $start = time_to_minutes($time);
        $code = generate_booking_code();
        $insert = $db->prepare(
            'INSERT INTO bookings (code, service_id, customer_name, customer_phone, booking_date, start_time, end_time, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $insert->execute([
            $code,
            $service['id'],
            $name,
            $phone,
            $date->format('Y-m-d'),
            minutes_to_time($start),
            minutes_to_time($start + (int) $service['duration_minutes']),
            date('Y-m-d H:i:s'),
        ]);

        return $code;
    } finally {
        $release = $db->prepare('SELECT RELEASE_LOCK(?)');
        $release->execute([$lockName]);
    }
}

function find_booking_by_code(string $code): ?array
{
    $statement = db()->prepare(
        'SELECT b.code, b.customer_name, b.booking_date, b.start_time, b.end_time, b.status,
                s.name AS service_name, s.price, s.duration_minutes
         FROM bookings b
         JOIN services s ON s.id = b.service_id
         WHERE b.code = ?'
    );
    $statement->execute([$code]);

    return $statement->fetch() ?: null;
}
