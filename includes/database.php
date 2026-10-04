<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $config = require dirname(__DIR__) . '/config.php';
        $db = $config['db'];

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['name']),
                $db['user'],
                $db['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            // Az üzenet a gazdagépet és a felhasználónevet is tartalmazza, ezért csak a kód kerül a naplóba.
            error_log('Adatbázis-kapcsolati hiba, kód: ' . $e->getCode());
            http_response_code(503);
            exit('Az oldal ideiglenesen nem érhető el. Kérjük, próbáld újra később.');
        }
    }

    return $pdo;
}
