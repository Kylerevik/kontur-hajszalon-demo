<?php
declare(strict_types=1);

const HU_MONTHS = [
    'január', 'február', 'március', 'április', 'május', 'június',
    'július', 'augusztus', 'szeptember', 'október', 'november', 'december',
];
const HU_WEEKDAYS = ['hétfő', 'kedd', 'szerda', 'csütörtök', 'péntek', 'szombat', 'vasárnap'];

const BOOKING_STATUSES = [
    'confirmed' => 'Visszaigazolt',
    'completed' => 'Teljesítve',
    'cancelled' => 'Lemondva',
    'no_show' => 'Nem jelent meg',
];

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_secure' => !empty($_SERVER['HTTPS']),
            'use_strict_mode' => true,
        ]);
    }
}

function csrf_token(): string
{
    ensure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_is_valid(): bool
{
    ensure_session();
    $sent = $_POST['csrf_token'] ?? '';

    return is_string($sent)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $sent);
}

function field_error(array $errors, string $field): string
{
    return isset($errors[$field])
        ? '<div class="invalid-feedback d-block">' . e($errors[$field]) . '</div>'
        : '';
}

function flash(string $type, string $message): void
{
    ensure_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    ensure_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function is_admin(): bool
{
    ensure_session();

    return isset($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('login.php');
    }
}

function format_price(int $price): string
{
    return number_format($price, 0, '', "\u{00A0}") . "\u{00A0}Ft";
}

function format_duration(int $minutes): string
{
    $hours = intdiv($minutes, 60);
    $rest = $minutes % 60;

    if ($hours === 0) {
        return $rest . ' perc';
    }

    return $rest === 0 ? $hours . ' óra' : $hours . ' óra ' . $rest . ' perc';
}

function format_time(string $time): string
{
    return substr($time, 0, 5);
}

function format_date_hu(string $date): string
{
    $day = new DateTimeImmutable($date);

    return sprintf(
        '%d. %s %d., %s',
        (int) $day->format('Y'),
        HU_MONTHS[(int) $day->format('n') - 1],
        (int) $day->format('j'),
        HU_WEEKDAYS[(int) $day->format('N') - 1]
    );
}

function phone_link(string $phone): string
{
    return 'tel:' . preg_replace('/[^\d+]/', '', $phone);
}

/** Statikus fájl útvonala módosítási idővel, hogy frissítés után ne ragadjon be a böngésző gyorsítótárában. */
function asset(string $path, string $prefix = ''): string
{
    return $prefix . $path . '?v=' . filemtime(dirname(__DIR__) . '/' . $path);
}
