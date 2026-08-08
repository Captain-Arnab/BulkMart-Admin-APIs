<?php
/**
 * Migration runner — executes database/migrations/*.sql in order.
 * Tracks applied files in `migrations` table.
 *
 * Usage: php scripts/migrate.php
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';

$migrationsDir = dirname(__DIR__) . '/database/migrations';

function vc_pdo_connect(): PDO
{
    try {
        return db();
    } catch (Throwable $e) {
        $c = db_config();
        $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $c['host'], $c['port'], $c['charset']);
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
        ]);
        $db = str_replace('`', '``', $c['name']);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$db}`");
        return $pdo;
    }
}

/** Split SQL file into executable statements (ignores comment-only chunks). */
function vc_split_sql(string $sql): array
{
    $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
    $parts = preg_split('/;\s*[\r\n]+/', $sql) ?: [];
    $out = [];
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '' || $part === ';') {
            continue;
        }
        $out[] = rtrim($part, ';');
    }
    return $out;
}

$pdo = vc_pdo_connect();

$pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT UNSIGNED NOT NULL,
  `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_migrations_migration` (`migration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$applied = $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
$applied = array_flip($applied);

$files = glob($migrationsDir . '/*.sql') ?: [];
natcasesort($files);

$batch = ((int) $pdo->query('SELECT COALESCE(MAX(batch), 0) FROM migrations')->fetchColumn()) + 1;
$ran = 0;

foreach ($files as $file) {
    $name = basename($file);
    if (isset($applied[$name])) {
        echo "[skip] $name\n";
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') {
        echo "[empty] $name\n";
        continue;
    }

    echo "[run]  $name ... ";
    try {
        foreach (vc_split_sql($sql) as $statement) {
            $pdo->exec($statement);
        }
        $stmt = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)');
        $stmt->execute([$name, $batch]);
        echo "OK\n";
        $ran++;
    } catch (Throwable $e) {
        echo "FAILED\n";
        fwrite(STDERR, $e->getMessage() . "\n");
        exit(1);
    }
}

echo $ran === 0 ? "Nothing to migrate.\n" : "Applied $ran migration(s) in batch $batch.\n";
