<?php
declare(strict_types=1);

const SLOT_STEP_MINUTES = 30;
const MIN_LEAD_MINUTES = 60;
const BOOKING_WINDOW_DAYS = 60;

function parse_date(string $value): ?DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

    // A createFromFormat átgörgeti a hibás dátumokat (pl. február 31.), ezért vissza kell vetni.
    return $date !== false && $date->format('Y-m-d') === $value ? $date : null;
}

function time_to_minutes(string $time): int
{
    return (int) substr($time, 0, 2) * 60 + (int) substr($time, 3, 2);
}

function minutes_to_time(int $minutes): string
{
    return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
}

function is_bookable_date(DateTimeImmutable $date, ?DateTimeImmutable $now = null): bool
{
    $today = ($now ?? new DateTimeImmutable())->setTime(0, 0);
    $last = $today->modify('+' . BOOKING_WINDOW_DAYS . ' days');

    return $date >= $today && $date <= $last;
}

function fetch_day_hours(PDO $db, DateTimeImmutable $date): ?array
{
    $statement = $db->prepare('SELECT open_time, close_time FROM opening_hours WHERE weekday = ?');
    $statement->execute([(int) $date->format('N')]);

    return $statement->fetch() ?: null;
}

function overlaps_any(int $start, int $end, array $busyRanges): bool
{
    foreach ($busyRanges as [$busyStart, $busyEnd]) {
        if ($start < $busyEnd && $end > $busyStart) {
            return true;
        }
    }

    return false;
}

/** A szolgáltatás időtartamába belefér, ütközésmentes kezdési időpontok (ÓÓ:PP). */
function available_slots(PDO $db, DateTimeImmutable $date, array $service, ?DateTimeImmutable $now = null): array
{
    $now ??= new DateTimeImmutable();
    $hours = fetch_day_hours($db, $date);

    if ($hours === null || !is_bookable_date($date, $now)) {
        return [];
    }

    $statement = $db->prepare(
        "SELECT start_time, end_time FROM bookings WHERE booking_date = ? AND status <> 'cancelled'"
    );
    $statement->execute([$date->format('Y-m-d')]);
    $busyRanges = array_map(
        fn(array $row): array => [time_to_minutes($row['start_time']), time_to_minutes($row['end_time'])],
        $statement->fetchAll()
    );

    $earliest = 0;
    if ($date->format('Y-m-d') === $now->format('Y-m-d')) {
        $earliest = time_to_minutes($now->format('H:i')) + MIN_LEAD_MINUTES;
    }

    $duration = (int) $service['duration_minutes'];
    $closing = time_to_minutes($hours['close_time']);
    $slots = [];

    for ($start = time_to_minutes($hours['open_time']); $start + $duration <= $closing; $start += SLOT_STEP_MINUTES) {
        if ($start >= $earliest && !overlaps_any($start, $start + $duration, $busyRanges)) {
            $slots[] = minutes_to_time($start);
        }
    }

    return $slots;
}
