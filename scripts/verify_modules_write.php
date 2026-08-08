<?php

declare(strict_types=1);

/**
 * Functional write-path checks for Step 11 modules.
 */

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

$c = tempnam(sys_get_temp_dir(), 'vcw');
$r = req('POST', "$base/login", $c, ['identity' => 'admin@veggiicart.com', 'password' => 'ChangeMe@123']);
ok('Login', in_array($r['code'], [302, 301], true));

// Customer KYC approve (idempotent)
$r = req('GET', "$base/customers/1", $c);
ok('Customer detail', $r['code'] === 200 && str_contains($r['body'], 'KYC'));
$r = req('POST', "$base/customers/1/approve", $c, []);
ok('KYC approve', in_array($r['code'], [302, 301], true));

// Offer create
$r = req('POST', "$base/offers", $c, [
    'title' => 'VERIFY Demo Offer',
    'discount_type' => 'percentage',
    'discount_value' => '5',
    'min_qty' => '1',
    'category_id' => '',
    'coupon_code' => 'VERIFY5',
    'valid_from' => date('Y-m-d\TH:i'),
    'valid_till' => date('Y-m-d\TH:i', strtotime('+7 days')),
    'is_active' => '1',
]);
ok('Offer create', in_array($r['code'], [302, 301], true));

// Market prices save (product 1)
$r = req('POST', "$base/market-prices/save", $c, [
    'prices' => ['1' => '99.50'],
]);
ok('Market prices save', in_array($r['code'], [302, 301], true));

// Support reply
$r = req('GET', "$base/support", $c);
ok('Support list', $r['code'] === 200);
if (preg_match('/\/support\/(\d+)/', $r['body'], $m)) {
    $tid = $m[1];
    $r = req('POST', "$base/support/{$tid}/reply", $c, [
        'message' => 'VERIFY admin reply — please ignore.',
    ]);
    ok('Support reply', in_array($r['code'], [302, 301], true));
    $r = req('POST', "$base/support/{$tid}/status", $c, ['status' => 'in_progress']);
    ok('Support status', in_array($r['code'], [302, 301], true));
} else {
    ok('Support reply', false);
    ok('Support status', false);
}

// Reports + CSV
$from = date('Y-m-d', strtotime('-30 days'));
$to = date('Y-m-d');
$r = req('GET', "$base/reports?from={$from}&to={$to}", $c);
ok('Reports range', $r['code'] === 200 && (str_contains($r['body'], 'Total orders') || str_contains($r['body'], 'total orders') || str_contains($r['body'], 'Orders')));
$r = req('GET', "$base/reports/export?from={$from}&to={$to}", $c);
ok('Reports CSV', $r['code'] === 200 && (str_contains($r['body'], 'order') || str_contains($r['body'], 'Order') || str_starts_with(ltrim($r['body']), '"')));

// Settings app save
$r = req('POST', "$base/settings/app", $c, [
    'support_phone' => '9999999999',
    'support_email' => 'support@veggiicart.test',
    'company_name' => 'VeggiiCart Demo',
]);
ok('Settings app', in_array($r['code'], [302, 301], true));

// Roles list
$r = req('GET', "$base/roles", $c);
ok('Roles list', $r['code'] === 200 && str_contains($r['body'], 'subadmin@veggiicart.com'));

@unlink($c);
echo PHP_EOL . ($fail === 0 ? "All write checks passed.\n" : "$fail failed.\n");
exit($fail === 0 ? 0 : 1);
