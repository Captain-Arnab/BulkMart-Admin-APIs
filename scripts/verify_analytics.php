<?php

declare(strict_types=1);

$base = 'http://localhost/VGS/veggiicart/public';
$fail = 0;

function req(string $method, string $url, string $cookieFile, ?array $post = null): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_CUSTOMREQUEST => $method,
    ]);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    $headers = substr((string) $raw, 0, $headerSize);
    $body = substr((string) $raw, $headerSize);
    $location = '';
    if (preg_match('/^Location:\s*(.+)$/mi', $headers, $m)) {
        $location = trim($m[1]);
    }
    return ['code' => $code, 'body' => $body, 'location' => $location];
}

function ok(string $label, bool $pass): void
{
    global $fail;
    echo ($pass ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL;
    if (!$pass) {
        $fail++;
    }
}

$c = tempnam(sys_get_temp_dir(), 'vca');
$r = req('POST', "$base/login", $c, ['identity' => 'admin@veggiicart.com', 'password' => 'ChangeMe@123']);
ok('Login', in_array($r['code'], [302, 301], true));

$r = req('GET', "$base/dashboard", $c);
ok('Dashboard 200', $r['code'] === 200);
ok('Dashboard has trend chart', str_contains($r['body'], 'id="chart-trend"'));
ok('Dashboard has status chart', str_contains($r['body'], 'id="chart-status"'));
ok('Dashboard has categories chart', str_contains($r['body'], 'id="chart-categories"'));
ok('Dashboard has sparklines', str_contains($r['body'], 'id="spark-orders"'));
ok('Dashboard has top products', str_contains($r['body'], 'Top 5 Products'));
ok('Dashboard has low stock', str_contains($r['body'], 'Low Stock Watchlist'));
ok('Dashboard embeds chart JSON', str_contains($r['body'], 'VC_DASHBOARD'));
ok('Dashboard loads analytics JS', str_contains($r['body'], 'vc-dashboard.js'));
ok('No coming soon on dashboard', !str_contains($r['body'], 'Getting started'));

$r = req('GET', "$base/reports?preset=30d", $c);
ok('Reports 200', $r['code'] === 200);
ok('Reports summary strip', str_contains($r['body'], 'Total revenue'));
ok('Reports trend', str_contains($r['body'], 'id="report-trend"'));
ok('Reports status donut', str_contains($r['body'], 'id="report-status"'));
ok('Reports category charts', str_contains($r['body'], 'id="report-category-bars"') && str_contains($r['body'], 'id="report-category-share"'));
ok('Reports products table', str_contains($r['body'], 'section-products'));
ok('Reports customers table', str_contains($r['body'], 'section-customers'));
ok('Reports order detail', str_contains($r['body'], 'orders-detail-table'));
ok('Reports export link', str_contains($r['body'], 'reports/export'));
ok('Reports loads JS', str_contains($r['body'], 'vc-reports.js'));

$r = req('GET', "$base/reports/export?preset=30d", $c);
ok('CSV export', $r['code'] === 200 && (str_contains($r['body'], 'order_number') || str_contains($r['body'], 'VC-')));

require dirname(__DIR__) . '/app/config/app.php';
require dirname(__DIR__) . '/app/config/db.php';
$demo = (int) db()->query("SELECT COUNT(*) FROM orders WHERE order_number LIKE 'VC-DEMO-%'")->fetchColumn();
ok('DEMO analytics orders seeded', $demo >= 40);

@unlink($c);
echo PHP_EOL . ($fail === 0 ? "All analytics checks passed.\n" : "$fail failed.\n");
exit($fail === 0 ? 0 : 1);
