<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

if (is_admin()) {
    redirect('bookings.php');
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_is_valid()) {
        $error = 'Az űrlap érvényessége lejárt. Kérjük, próbáld újra.';
    } else {
        $statement = db()->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
        $statement->execute([$username]);
        $admin = $statement->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            redirect('bookings.php');
        }

        // A késleltetés lassítja a jelszótalálgatást.
        sleep(1);
        $error = 'Hibás felhasználónév vagy jelszó.';
    }
}

$pageTitle = 'Belépés – Kontúr admin';

require dirname(__DIR__) . '/includes/admin_header.php';
?>
<div class="login-card">
    <h1 class="login-card__title">Admin belépés</h1>

    <div class="demo-credentials" role="note">
        <p class="demo-credentials__title">Demó belépési adatok</p>
        <dl class="mb-0">
            <dt>Felhasználónév</dt>
            <dd><code>demo</code></dd>
            <dt>Jelszó</dt>
            <dd><code>Szalon2026!</code></dd>
        </dl>
    </div>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
<?php endif; ?>

    <form method="post" action="login.php">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="username">Felhasználónév</label>
            <input class="form-control" type="text" id="username" name="username" value="<?= e($username) ?>" maxlength="40" autocomplete="username" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password">Jelszó</label>
            <input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button class="btn btn-accent w-100" type="submit">Belépés</button>
    </form>
</div>
<?php require dirname(__DIR__) . '/includes/admin_footer.php'; ?>
