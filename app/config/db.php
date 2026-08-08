<?php
/**
 * PDO MySQL connection.
 * Credentials come from config.local.php (see config.sample.php).
 */

require_once __DIR__ . '/app.php';

function db_config(): array
{
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }

    $defaults = [
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'name'    => 'veggiicart',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ];

    $local = [];
    $localFile = __DIR__ . '/config.local.php';
    if (is_file($localFile)) {
        $local = require $localFile;
    }

    $cfg = array_merge($defaults, $local['db'] ?? []);
    return $cfg;
}

/**
 * Shared PDO instance. Throws PDOException on failure.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $c = db_config();
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $c['host'],
        $c['port'],
        $c['name'],
        $c['charset']
    );

    $pdo = new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);

    return $pdo;
}

/**
 * Lightweight connectivity check (does not require tables).
 * Returns ['ok' => bool, 'message' => string]
 */
function db_ping(): array
{
    try {
        $pdo = db();
        $pdo->query('SELECT 1');
        return ['ok' => true, 'message' => 'DB connected'];
    } catch (Throwable $e) {
        $msg = APP_DEBUG ? $e->getMessage() : 'Database connection failed';
        return ['ok' => false, 'message' => $msg];
    }
}
