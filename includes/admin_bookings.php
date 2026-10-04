<?php
declare(strict_types=1);

const ADMIN_VIEWS = [
    'upcoming' => 'Közelgő foglalások',
    'past' => 'Korábbi foglalások',
    'all' => 'Összes foglalás',
];

function handle_booking_action(PDO $db, array $post): array
{
    if (!csrf_is_valid()) {
        return ['danger', 'Az űrlap érvényessége lejárt. Kérjük, próbáld újra.'];
    }

    $id = filter_var($post['booking_id'] ?? '', FILTER_VALIDATE_INT);
    $action = $post['action'] ?? '';
    $status = $post['status'] ?? '';

    if ($id !== false && $action === 'status' && is_string($status) && isset(BOOKING_STATUSES[$status])) {
        $db->prepare('UPDATE bookings SET status = ? WHERE id = ?')->execute([$status, $id]);

        return ['success', 'A foglalás állapota módosítva.'];
    }

    if ($id !== false && $action === 'delete') {
        $db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$id]);

        return ['success', 'A foglalás törölve.'];
    }

    return ['danger', 'A kért művelet nem hajtható végre.'];
}

function read_booking_filters(array $query): array
{
    $view = $query['view'] ?? 'upcoming';
    $status = $query['status'] ?? '';
    $date = is_string($query['date'] ?? null) ? parse_date($query['date']) : null;

    return [
        'view' => is_string($view) && isset(ADMIN_VIEWS[$view]) ? $view : 'upcoming',
        'status' => is_string($status) && isset(BOOKING_STATUSES[$status]) ? $status : '',
        'date' => $date?->format('Y-m-d') ?? '',
    ];
}

function fetch_admin_bookings(PDO $db, array $filters, string $today): array
{
    $conditions = [];
    $params = [];

    if ($filters['date'] !== '') {
        $conditions[] = 'b.booking_date = ?';
        $params[] = $filters['date'];
    } elseif ($filters['view'] === 'upcoming') {
        $conditions[] = 'b.booking_date >= ?';
        $params[] = $today;
    } elseif ($filters['view'] === 'past') {
        $conditions[] = 'b.booking_date < ?';
        $params[] = $today;
    }

    if ($filters['status'] !== '') {
        $conditions[] = 'b.status = ?';
        $params[] = $filters['status'];
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $direction = $filters['view'] === 'past' && $filters['date'] === '' ? 'DESC' : 'ASC';

    $statement = $db->prepare(
        "SELECT b.id, b.code, b.customer_name, b.customer_phone, b.booking_date, b.start_time, b.end_time,
                b.status, b.created_at, s.name AS service_name, s.price
         FROM bookings b
         JOIN services s ON s.id = b.service_id
         $where
         ORDER BY b.booking_date $direction, b.start_time $direction"
    );
    $statement->execute($params);

    return $statement->fetchAll();
}

/** A visszaigazolt foglalások száma: ma, a következő 7 napban és összesen a mai naptól. */
function fetch_booking_stats(PDO $db, DateTimeImmutable $today): array
{
    $statement = $db->prepare(
        "SELECT COALESCE(SUM(booking_date = ?), 0) AS today_count,
                COALESCE(SUM(booking_date BETWEEN ? AND ?), 0) AS week_count,
                COALESCE(SUM(booking_date >= ?), 0) AS upcoming_count
         FROM bookings
         WHERE status = 'confirmed'"
    );
    $todayValue = $today->format('Y-m-d');
    $statement->execute([$todayValue, $todayValue, $today->modify('+6 days')->format('Y-m-d'), $todayValue]);

    return array_map('intval', $statement->fetch());
}
