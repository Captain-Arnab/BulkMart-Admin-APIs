<?php
/**
 * Smoke test: login + sidebar routes (CLI / curl-style via stream).
 * Run: php scripts/smoke_test.php
 */

$base = getenv('VC_BASE') ?: 'http://localhost/VGS/veggiicart/public';
$cookie = tempnam(sys_get_temp_dir(), 'vc_cookie');

function req(string $method, string $url, ?string $cookieFile = null, array $opts = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER         => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_TIMEOUT        => 15,
    ]);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    if (!empty($opts['post'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opts['post']));
    }
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false) {
        return ['code' => 0, 'headers' => '', 'body' => $err, 'location' => null];
    }
    $parts = explode("\r\n\r\n", $raw, 2);
    $headers = $parts[0] ?? '';
    $body = $parts[1] ?? '';
    $location = null;
    if (preg_match('/^Location:\s*(.+)$/mi', $headers, $m)) {
        $location = trim($m[1]);
    }
    return compact('code', 'headers', 'body', 'location');
}

$fail = 0;
function ok(string $label, bool $pass): void
{
    global $fail;
    echo ($pass ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL;
    if (!$pass) {
        $fail++;
    }
}

// 1) Login page
$r = req('GET', "$base/login");
ok('GET /login => 200', $r['code'] === 200);
ok('Login shows brand', str_contains($r['body'], 'VeggiiCart'));
ok('Login shows theme CSS', str_contains($r['body'], 'veggiicart-theme.css'));

// 2) Protected redirect
$r = req('GET', "$base/dashboard", $cookie);
ok('GET /dashboard unauth redirects', in_array($r['code'], [302, 301], true) && str_contains((string) $r['location'], 'login'));

// 3) Login POST
$r = req('POST', "$base/login", $cookie, [
    'post' => [
        'identity' => 'admin@veggiicart.com',
        'password' => 'ChangeMe@123',
    ],
]);
ok('POST /login success redirects', in_array($r['code'], [302, 301], true) && str_contains((string) $r['location'], 'dashboard'));

// 4) Dashboard after login
$r = req('GET', "$base/dashboard", $cookie);
ok('GET /dashboard auth => 200', $r['code'] === 200);
ok('Dashboard KPI Orders Today', str_contains($r['body'], 'Orders Today'));
ok('Dashboard KPI Low Stock', str_contains($r['body'], 'Low Stock'));
ok('Sidebar has Delivery', str_contains($r['body'], 'Delivery Management'));
ok('No leftover NiceAdmin title brand', !str_contains($r['body'], 'NiceAdmin</span>'));

// 5) Module routes
$routes = [
    'products',
    'products/add',
    'products/bulk-upload',
    'products/bulk-stock',
    'categories',
    'orders',
    'delivery',
    'customers',
    'roles',
    'offers',
    'market-prices',
    'support',
    'reports',
    'settings',
    'test/db',
];

foreach ($routes as $path) {
    $r = req('GET', "$base/$path", $cookie);
    $pass = $r['code'] === 200;
    if ($path === 'test/db') {
        $pass = $pass && str_contains($r['body'], 'DB connected');
    } else {
        // Modules are live; only reject leftover "Coming soon" placeholders.
        $pass = $pass && !str_contains($r['body'], 'Coming soon');
    }
    ok("GET /$path", $pass);
}

// 6) Bad login
@unlink($cookie);
$cookie2 = tempnam(sys_get_temp_dir(), 'vc_cookie');
$r = req('POST', "$base/login", $cookie2, [
    'post' => ['identity' => 'admin@veggiicart.com', 'password' => 'wrong'],
]);
ok('Bad password redirects to login', in_array($r['code'], [302, 301], true) && str_contains((string) $r['location'], 'login'));

@unlink($cookie);
@unlink($cookie2);

echo PHP_EOL . ($fail === 0 ? "All checks passed.\n" : "$fail check(s) failed.\n");
exit($fail === 0 ? 0 : 1);
