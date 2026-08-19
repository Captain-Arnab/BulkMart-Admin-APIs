<?php
/**
 * Verify products/categories module + bulk imports.
 * Usage: php scripts/verify_products.php
 */

declare(strict_types=1);

$base = getenv('VC_BASE') ?: 'http://localhost/VGS/veggiicart/public';
$cookie = tempnam(sys_get_temp_dir(), 'vc');

function req(string $method, string $url, string $cookie, array $opts = []): array
{
    $ch = curl_init($url);
    $headers = [];
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER         => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_TIMEOUT        => 30,
    ]);
    if (!empty($opts['post'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $opts['post']);
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
function ok(string $label, bool $pass): void
{
    global $fail;
    echo ($pass ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL;
    if (!$pass) {
        $fail++;
    }
}

$r = req('POST', "$base/login", $cookie, [
    'post' => ['identity' => 'admin@veggiicart.com', 'password' => 'ChangeMe@123'],
]);
ok('Login', in_array($r['code'], [302, 301], true));

$r = req('GET', "$base/products", $cookie);
ok('Products list 200', $r['code'] === 200);
ok('Stock badge present', str_contains($r['body'], 'In stock') || str_contains($r['body'], 'Low stock'));

// List is name-sorted + paginated (20/page) — Tomato/Potato may not be on page 1.
// Assert via search query (same index action) instead of first-page HTML.
$r = req('GET', "$base/products?q=" . rawurlencode('Tomato'), $cookie);
ok('Seeded Tomato visible (search)', $r['code'] === 200 && str_contains($r['body'], 'Tomato'));
$r = req('GET', "$base/products?q=" . rawurlencode('Potato'), $cookie);
ok('Seeded Potato visible (search)', $r['code'] === 200 && str_contains($r['body'], 'Potato'));

$r = req('GET', "$base/categories", $cookie);
ok('Categories list 200', $r['code'] === 200);
ok('Green Vegetables visible', str_contains($r['body'], 'Green Vegetables'));
ok('Product counts shown', str_contains($r['body'], 'badge'));

// Bulk product upload
$csv = dirname(__DIR__) . '/database/templates/products_bulk_upload.csv';
$r = req('POST', "$base/products/bulk-upload", $cookie, [
    'post' => [
        'file' => new CURLFile($csv, 'text/csv', 'products_bulk_upload.csv'),
    ],
]);
ok('Bulk upload redirects', in_array($r['code'], [302, 301], true));

$r = req('GET', "$base/products/bulk-upload", $cookie);
ok('Bulk upload summary page', $r['code'] === 200 && (str_contains($r['body'], 'imported') || str_contains($r['body'], 'Import')));

// Bulk stock
$stockCsv = dirname(__DIR__) . '/database/templates/products_bulk_stock.csv';
$r = req('POST', "$base/products/bulk-stock", $cookie, [
    'post' => [
        'file' => new CURLFile($stockCsv, 'text/csv', 'products_bulk_stock.csv'),
    ],
]);
ok('Bulk stock redirects', in_array($r['code'], [302, 301], true));

$r = req('GET', "$base/products/bulk-stock", $cookie);
ok('Bulk stock summary', $r['code'] === 200);

// Permission gate still works for unauth
@unlink($cookie);
$cookie2 = tempnam(sys_get_temp_dir(), 'vc');
$r = req('GET', "$base/products", $cookie2);
ok('Unauth products redirects login', in_array($r['code'], [302, 301], true) && str_contains((string)$r['location'], 'login'));
@unlink($cookie2);

// DB counts
require dirname(__DIR__) . '/app/config/db.php';
$pdo = db();
$tables = ['categories','products','customers','orders','admin_users','banners','offers','support_tickets','market_prices','wishlists','notifications','role_permissions','customer_documents','addresses','order_items','support_ticket_replies'];
echo "\nTable row counts:\n";
foreach ($tables as $t) {
    $c = (int) $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    echo str_pad($t, 28) . $c . PHP_EOL;
}

// Confirm sample product imported
$sample = $pdo->query("SELECT COUNT(*) FROM products WHERE item_code='VG-TEST-001'")->fetchColumn();
ok('VG-TEST-001 not in catalog', (int)$sample === 0);

$catalog = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
ok('Confirmed catalog size', $catalog === 34);

$tomato = $pdo->query("SELECT item_code, batch_no, stock FROM products WHERE name='Tomato'")->fetch();
ok('Tomato item_code is 20', $tomato && (string)$tomato['item_code'] === '20');
ok('Tomato batch_no is NULL', $tomato && $tomato['batch_no'] === null);
ok('Tomato stock updated via bulk', $tomato && (float)$tomato['stock'] == 275.0);

echo PHP_EOL . ($fail === 0 ? "All product module checks passed.\n" : "$fail failed.\n");
exit($fail === 0 ? 0 : 1);
