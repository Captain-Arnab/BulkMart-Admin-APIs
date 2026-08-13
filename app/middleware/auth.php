<?php
/**
 * Session / login gate.
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

function auth_home_path(?array $user = null): string
{
    $user = $user ?? auth_user();
    if ($user && ($user['role'] ?? '') === 'delivery_manager') {
        return 'delivery';
    }
    return 'dashboard';
}

function require_auth(): void
{
    if (!auth_check()) {
        flash('error', 'Please sign in to continue.');
        redirect('login');
    }
}

/**
 * Login against admin_users only (no hard-coded seed fallback).
 */
function attempt_login(string $identity, string $password): bool
{
    $identity = trim($identity);
    if ($identity === '' || $password === '') {
        return false;
    }

    try {
        $admins = new AdminUser();
        $email = strtolower($identity);
        // Convenience: username "admin" maps to seeded super-admin email in DB
        if ($email === strtolower(SEED_ADMIN_USERNAME)) {
            $email = strtolower(SEED_ADMIN_EMAIL);
        }
        $row = $admins->findByEmail($email);
        if ($row && (int) $row['is_active'] === 1 && password_verify($password, $row['password_hash'])) {
            $perms = null;
            if ($row['role_type'] !== 'super_admin') {
                $perms = $admins->moduleKeys((int) $row['id']);
                if ($row['role_type'] === 'delivery_manager') {
                    $perms = array_values(array_unique(array_merge($perms, ['delivery', 'profile'])));
                } elseif ($row['role_type'] === 'sub_admin') {
                    // Dashboard is always available for sub-admins
                    $perms = array_values(array_unique(array_merge($perms, ['dashboard'])));
                }
            }
            auth_login([
                'id'                 => (int) $row['id'],
                'name'               => $row['name'],
                'email'              => $row['email'],
                'username'           => explode('@', $row['email'])[0],
                'role'               => $row['role_type'],
                'module_permissions' => $perms,
            ]);
            return true;
        }
    } catch (Throwable $e) {
        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log('attempt_login DB error: ' . $e->getMessage());
        }
    }

    return false;
}
