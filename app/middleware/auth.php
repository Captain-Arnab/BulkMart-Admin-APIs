<?php
/**
 * Session / login gate.
 * Public routes (login) skip this; everything else redirects to /login.
 */

function auth_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => app_base_url() === '' ? '/' : app_base_url() . '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function auth_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function auth_check(): bool
{
    return auth_user() !== null;
}

function auth_login(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['auth_user'] = $user;
    $_SESSION['auth_login_at'] = time();
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', $p['secure'] ?? false, $p['httponly'] ?? true);
    }
    session_destroy();
}

/**
 * Middleware: require authenticated session.
 */
function require_auth(): void
{
    if (!auth_check()) {
        flash('error', 'Please sign in to continue.');
        redirect('login');
    }
}

/**
 * Attempt login against seeded Super Admin (pre-DB).
 * Later: replace with admin_users table lookup.
 */
function attempt_seed_login(string $identity, string $password): bool
{
    $identity = trim(strtolower($identity));
    $okIdentity = ($identity === strtolower(SEED_ADMIN_EMAIL) || $identity === strtolower(SEED_ADMIN_USERNAME));
    if (!$okIdentity || !hash_equals(SEED_ADMIN_PASSWORD, $password)) {
        return false;
    }

    auth_login([
        'id'                 => 1,
        'name'               => SEED_ADMIN_NAME,
        'email'              => SEED_ADMIN_EMAIL,
        'username'           => SEED_ADMIN_USERNAME,
        'role'               => SEED_ADMIN_ROLE,
        // null = all modules (Super Admin)
        'module_permissions' => null,
    ]);
    return true;
}
