<?php
/**
 * Application constants and bootstrap helpers.
 */

$local = [];
$localFile = __DIR__ . '/config.local.php';
if (is_file($localFile)) {
    $local = require $localFile;
}

$appCfg = $local['app'] ?? [];

define('APP_NAME', 'VeggiiCart Admin');
define('APP_VERSION', '0.1.0');
define('APP_ROOT', dirname(__DIR__, 2));
define('APP_PATH', dirname(__DIR__));
define('PUBLIC_PATH', APP_ROOT . '/public');
define('VIEW_PATH', APP_PATH . '/views');

define('SESSION_LIFETIME', (int) ($appCfg['session_lifetime'] ?? 7200));
define('APP_DEBUG', (bool) ($appCfg['debug'] ?? true));

/**
 * Brand palette (mirrors Flutter app tokens).
 */
define('BRAND_PRIMARY', '#12833B');
define('BRAND_DEEP_FOREST', '#0B5C27');
define('BRAND_INK', '#1E1F22');
define('BRAND_BG', '#FFFFFF');
define('BRAND_BG_SECTION', '#F4FAF6');
define('BRAND_ACCENT', '#F5A623');
define('BRAND_ERROR', '#D64545');

/**
 * TEMP seeded Super Admin — CHANGE BEFORE GOING LIVE.
 * Used until admin_users / roles tables exist.
 */
define('SEED_ADMIN_EMAIL', 'admin@veggiicart.com');
define('SEED_ADMIN_USERNAME', 'admin');
define('SEED_ADMIN_PASSWORD', 'ChangeMe@123'); // TEMP — change before production
define('SEED_ADMIN_NAME', 'Super Admin');
define('SEED_ADMIN_ROLE', 'super_admin');

/**
 * Resolve public base URL (no trailing slash), e.g. /VGS/veggiicart/public
 */
function app_base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $local = [];
    $localFile = __DIR__ . '/config.local.php';
    if (is_file($localFile)) {
        $local = require $localFile;
    }
    $configured = trim((string) (($local['app']['base_url'] ?? '')), '/');
    if ($configured !== '') {
        $base = '/' . $configured;
        return $base;
    }

    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = str_replace('\\', '/', dirname($script));
    $base = rtrim($dir === '/' ? '' : $dir, '/');
    return $base;
}

/** Absolute URL path helper: url('dashboard') => /.../public/dashboard */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = app_base_url();
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return ($base === '' ? '' : $base) . '/' . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $val = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $val;
}
