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
 * Local seed / TEST-ONLY admin credentials used by scripts/seed*.php.
 * These are NEVER a login bypass — auth requires a real admin_users row.
 * Do not use these passwords in production; rotate before go-live.
 * Demo login hints on /login only render when APP_DEBUG is true.
 */
define('SEED_ADMIN_EMAIL', 'admin@veggiicart.com');
define('SEED_ADMIN_USERNAME', 'admin');
define('SEED_ADMIN_PASSWORD', 'ChangeMe@123'); // TEST-ONLY — rotate before production
define('SEED_ADMIN_NAME', 'Super Admin');
define('SEED_ADMIN_ROLE', 'super_admin');
define('TEST_ONLY_ADMIN_NOTE', 'TEST-ONLY credentials for local/dev — remove or rotate before go-live');

/**
 * Nested config lookup: app_config('jwt.secret'), app_config('sms.enabled')
 */
function app_config(string $key, mixed $default = null): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = [];
        $localFile = __DIR__ . '/config.local.php';
        if (is_file($localFile)) {
            $cfg = require $localFile;
        }
    }
    $parts = explode('.', $key);
    $cursor = $cfg;
    foreach ($parts as $part) {
        if (!is_array($cursor) || !array_key_exists($part, $cursor)) {
            return $default;
        }
        $cursor = $cursor[$part];
    }
    return $cursor;
}

/** Whether new business registrations require manual admin KYC approval before use. */
function kyc_manual_review_enabled(): bool
{
    return AppSetting::manualReviewEnabled();
}

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

/**
 * Always the admin/public folder URL (no trailing slash), e.g. /VGS/veggiicart/public.
 * Works from both /public (admin) and website pages under / or /web.
 */
function public_base_url(): string
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

    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $script = rtrim($script === '/' ? '' : $script, '/');
    if (str_ends_with($script, '/web')) {
        $script = substr($script, 0, -4);
    }
    if ($script === '/public' || str_ends_with($script, '/public')) {
        $base = $script;
        return $base;
    }
    $base = ($script === '' ? '' : $script) . '/public';
    return $base;
}

function public_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = public_base_url();
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return ($base === '' ? '' : $base) . '/' . $path;
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

/** Public upload / media URL helper */
function media(?string $path, string $fallback = ''): string
{
    if ($path === null || $path === '') {
        return $fallback;
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return url(ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Display names as Title Case: "cabbage (small size)" → "Cabbage (Small Size)". Does not change stored data. */
function display_name(?string $value): string
{
    $s = mb_strtolower(trim((string) $value), 'UTF-8');
    if ($s === '') {
        return '';
    }
    return (string) preg_replace_callback(
        '/(^|[^\p{L}\p{N}])(\p{L})/u',
        static function (array $m): string {
            return $m[1] . mb_strtoupper($m[2], 'UTF-8');
        },
        $s
    );
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

define('VC_BANNER_TITLE_MAX', 160);
define('VC_BANNER_DESC_MAX', 500);
define('VC_BANNER_LINK_MAX', 500);
define('VC_PRODUCT_NAME_MAX', 160);
define('VC_PRODUCT_DESC_MAX', 2000);
define('VC_CATEGORY_NAME_MAX', 120);

require_once APP_PATH . '/helpers/media.php';
