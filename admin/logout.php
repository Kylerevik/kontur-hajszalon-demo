<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

allow_methods(['POST']);

if (csrf_is_valid()) {
    $_SESSION = [];
    session_destroy();
}

redirect('login.php');
