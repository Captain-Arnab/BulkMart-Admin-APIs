<?php
/**
 * Smoke-test remaining modules + sub-admin gating.
 * Usage: php scripts/verify_modules.php
 */
declare(strict_types=1);

$base = getenv('VC_BASE') ?: 'http://localhost/VGS/veggiicart/public';

function req(string $method, string $url, string $cookie, $post = null): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_TIMEOUT => 30,
    ]);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $parts = explode("\r\n\r\n", (string) $raw, 2);
    $headers = $parts[0] ?? '';
    $body = $parts[1] ?? '';
    $location = null;
    if (preg_match('/^Location:\s*(.+)$/mi', $headers, $m)) {
        $location = trim($m[1]);
    }
    return compact('code', 'body', 'location');
}

$fail = 0;
function ok(string $l, bool $p): void
{
    global $fail;
    echo ($p ? '[PASS] ' : '[FAIL] ') . $l . PHP_EOL;
    if (!$p) {
        $fail++;
    }
}

// Super admin module pages
$c = tempnam(sys_get_temp_dir(), 'sa');
req('POST', "$base/login", $c, ['identity' => 'admin@veggiicart.com', 'password' => 'ChangeMe@123']);
foreach ([
    'customers' => 'Customers',
    'roles' => 'Roles',
    'offers' => 'Offers',
    'market-prices' => 'Market Prices',
    'support' => 'Support',
    'reports' => 'Reports',
    'settings' => 'Settings',
] as $path => $needle) {
    $r = req('GET', "$base/$path", $c);
    ok("SA GET /$path", $r['code'] === 200 && str_contains($r['body'], $needle));
}

// Customer detail if any
require dirname(__DIR__) . '/app/config/db.php';
$cid = (int) db()->query('SELECT id FROM customers ORDER BY id LIMIT 1')->fetchColumn();
if ($cid) {
    $r = req('GET', "$base/customers/$cid", $c);
    ok('SA customer detail', $r['code'] === 200 && str_contains($r['body'], 'KYC'));
}
@unlink($c);

// Sub-admin gating
$c2 = tempnam(sys_get_temp_dir(), 'sub');
$r = req('POST', "$base/login", $c2, ['identity' => 'subadmin@veggiicart.com', 'password' => 'SubAdmin@123']);
ok('Sub-admin login', in_array($r['code'], [302, 301], true));

$r = req('GET', "$base/dashboard", $c2);
ok('Sub-admin dashboard', $r['code'] === 200);
ok('Sub-admin sees Products', str_contains($r['body'], 'Products &amp; Stock') || str_contains($r['body'], 'Products & Stock'));
ok('Sub-admin sees Customers', str_contains($r['body'], 'Customers'));
ok('Sub-admin hides Orders', !str_contains($r['body'], '>Orders<'));
ok('Sub-admin hides Reports', !str_contains($r['body'], 'Reports &amp; Analytics') && !str_contains($r['body'], 'Reports & Analytics'));
ok('Sub-admin hides Roles', !str_contains($r['body'], 'Roles &amp; Sub-Admins') && !str_contains($r['body'], 'Roles & Sub-Admins'));

$r = req('GET', "$base/products", $c2);
ok('Sub-admin can open products', $r['code'] === 200);
$r = req('GET', "$base/customers", $c2);
ok('Sub-admin can open customers', $r['code'] === 200);
$r = req('GET', "$base/orders", $c2);
ok('Sub-admin blocked from orders', in_array($r['code'], [302, 301], true));
$r = req('GET', "$base/reports", $c2);
ok('Sub-admin blocked from reports', in_array($r['code'], [302, 301], true));
$r = req('GET', "$base/settings", $c2);
ok('Sub-admin can open settings (password)', $r['code'] === 200);
@unlink($c2);

echo PHP_EOL . ($fail === 0 ? "All module checks passed.\n" : "$fail failed.\n");
exit($fail === 0 ? 0 : 1);
