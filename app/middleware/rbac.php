<?php
/**
 * Role-based module access.
 *
 * module_permissions:
 *   - null / ['*']  => Super Admin — all modules
 *   - string[]      => Sub-Admin — only listed module keys
 *   - Delivery Manager typically: ['delivery', 'profile']
 */

/** Canonical module keys used by sidebar + route checks */
function rbac_modules(): array
{
    return [
        'dashboard'    => 'Dashboard',
        'products'     => 'Products & Stock',
        'categories'   => 'Categories',
        'orders'       => 'Orders',
        'delivery'     => 'Delivery Management',
        'customers'    => 'Customers',
        'roles'        => 'Roles & Sub-Admins',
        'offers'       => 'Offers & Banners',
        'market_prices'=> 'Market Prices',
        'support'      => 'Support Tickets',
        'reports'      => 'Reports & Analytics',
        'settings'     => 'Settings',
        'profile'      => 'Profile',
    ];
}

function rbac_user_permissions(?array $user = null): ?array
{
    $user = $user ?? auth_user();
    if ($user === null) {
        return [];
    }
    return $user['module_permissions'] ?? null;
}

function rbac_can(string $module, ?array $user = null): bool
{
    $perms = rbac_user_permissions($user);
    if ($perms === null) {
        return true; // Super Admin
    }
    if (in_array('*', $perms, true)) {
        return true;
    }
    return in_array($module, $perms, true);
}

/**
 * Middleware factory: require a module key.
 */
function require_module(string $module): callable
{
    return function () use ($module): void {
        require_auth();
        if (!rbac_can($module)) {
            http_response_code(403);
            flash('error', 'You do not have access to this module.');
            redirect('dashboard');
        }
    };
}

/**
 * Sidebar nav definition — filtered by rbac_can().
 */
function rbac_sidebar_items(?array $user = null): array
{
    $items = [
        [
            'key'   => 'dashboard',
            'label' => 'Dashboard',
            'icon'  => 'bi-grid',
            'route' => 'dashboard',
        ],
        [
            'key'      => 'products',
            'label'    => 'Products & Stock',
            'icon'     => 'bi-box-seam',
            'route'    => 'products',
            'children' => [
                ['label' => 'All Products', 'route' => 'products'],
                ['label' => 'Add Product', 'route' => 'products/add'],
                ['label' => 'Bulk Upload', 'route' => 'products/bulk-upload'],
                ['label' => 'Bulk Stock Update', 'route' => 'products/bulk-stock'],
            ],
        ],
        [
            'key'   => 'categories',
            'label' => 'Categories',
            'icon'  => 'bi-tags',
            'route' => 'categories',
        ],
        [
            'key'   => 'orders',
            'label' => 'Orders',
            'icon'  => 'bi-cart3',
            'route' => 'orders',
        ],
        [
            'key'   => 'delivery',
            'label' => 'Delivery Management',
            'icon'  => 'bi-truck',
            'route' => 'delivery',
        ],
        [
            'key'   => 'customers',
            'label' => 'Customers',
            'icon'  => 'bi-people',
            'route' => 'customers',
        ],
        [
            'key'   => 'roles',
            'label' => 'Roles & Sub-Admins',
            'icon'  => 'bi-shield-lock',
            'route' => 'roles',
        ],
        [
            'key'   => 'offers',
            'label' => 'Offers & Banners',
            'icon'  => 'bi-megaphone',
            'route' => 'offers',
        ],
        [
            'key'   => 'market_prices',
            'label' => 'Market Prices',
            'icon'  => 'bi-graph-up-arrow',
            'route' => 'market-prices',
        ],
        [
            'key'   => 'support',
            'label' => 'Support Tickets',
            'icon'  => 'bi-headset',
            'route' => 'support',
        ],
        [
            'key'   => 'reports',
            'label' => 'Reports & Analytics',
            'icon'  => 'bi-bar-chart',
            'route' => 'reports',
        ],
        [
            'key'   => 'settings',
            'label' => 'Settings',
            'icon'  => 'bi-gear',
            'route' => 'settings',
        ],
    ];

    return array_values(array_filter($items, static function (array $item) use ($user): bool {
        return rbac_can($item['key'], $user);
    }));
}

function rbac_is_active(string $route, string $currentPath): bool
{
    $route = trim($route, '/');
    $currentPath = trim($currentPath, '/');
    if ($route === $currentPath) {
        return true;
    }
    if ($route !== '' && str_starts_with($currentPath, $route . '/')) {
        return true;
    }
    return false;
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = app_base_url();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base)) ?: '/';
    }
    return trim($path, '/');
}
