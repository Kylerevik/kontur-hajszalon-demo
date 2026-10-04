<?php
declare(strict_types=1);

function fetch_services_by_category(): array
{
    $rows = db()->query(
        'SELECT id, category, name, description, duration_minutes, price
         FROM services
         WHERE is_active = 1
         ORDER BY sort_order, id'
    )->fetchAll();

    $grouped = [];
    foreach ($rows as $row) {
        $grouped[$row['category']][] = $row;
    }

    return $grouped;
}

function find_service(int $id): ?array
{
    $statement = db()->prepare(
        'SELECT id, category, name, description, duration_minutes, price
         FROM services
         WHERE id = ? AND is_active = 1'
    );
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function fetch_category_summaries(): array
{
    return db()->query(
        'SELECT category, MIN(price) AS min_price
         FROM services
         WHERE is_active = 1
         GROUP BY category
         ORDER BY MIN(sort_order)'
    )->fetchAll();
}

/** Nyitvatartás hétköznap szerint indexelve (1 = hétfő, 7 = vasárnap). */
function fetch_opening_hours(): array
{
    $rows = db()->query('SELECT weekday, open_time, close_time FROM opening_hours')->fetchAll();

    $hours = [];
    foreach ($rows as $row) {
        $hours[(int) $row['weekday']] = $row;
    }

    return $hours;
}
