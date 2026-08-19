<?php
/**
 * Two-account live-data proof for website account pages.
 * Usage: php scripts/verify_live_pages.php
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';

spl_autoload_register(static function (string $class): void {
    foreach ([
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/services/' . $class . '.php',
        APP_PATH . '/core/' . $class . '.php',
    ] as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$fail = 0;
function ok(bool $cond, string $msg): void
{
    global $fail;
    if ($cond) {
        echo "OK  $msg\n";
        return;
    }
    echo "FAIL  $msg\n";
    $fail++;
}

$apiBase = rtrim(getenv('VERIFY_API_BASE') ?: 'http://localhost/VGS/veggiicart/public/api/v1', '/');
$pdo = db();

function api_get(string $base, string $path, string $token): array
{
    $ch = curl_init($base . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ],
        CURLOPT_TIMEOUT => 20,
    ]);
    $raw = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode($raw, true);
    return ['code' => $code, 'json' => is_array($json) ? $json : null, 'raw' => $raw];
}

$rows = $pdo->query('SELECT id, owner_name, business_name, kyc_status, mobile, email, created_at FROM customers ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) < 2) {
    $mobile = '90000' . str_pad((string) random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
    $pdo->prepare(
        'INSERT INTO customers (mobile, email, business_name, owner_name, business_type, kyc_status)
         VALUES (?, ?, ?, ?, ?, ?)'
    )->execute([$mobile, $mobile . '@veggiicart.test', 'Approved Fresh Hub', 'Approved Owner', 'retailer', 'approved']);
    $rows = $pdo->query('SELECT id, owner_name, business_name, kyc_status, mobile, email, created_at FROM customers ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
}

$pending = null;
$approved = null;
foreach ($rows as $r) {
    if ($pending === null && ($r['kyc_status'] ?? '') === 'pending') {
        $pending = $r;
    }
    if ($approved === null && ($r['kyc_status'] ?? '') === 'approved') {
        $approved = $r;
    }
}
if ($pending === null) {
    $pending = $rows[0];
}
if ($approved === null) {
    $approved = $rows[count($rows) - 1];
    if ((int) $approved['id'] === (int) $pending['id'] && count($rows) > 1) {
        $approved = $rows[1];
    }
}
if ((int) $pending['id'] === (int) $approved['id']) {
    echo "Only one customer in DB; creating a second approved account for the two-account test.\n";
    $mobile = '91' . random_int(1000000000, 1999999999);
    $pdo->prepare(
        'INSERT INTO customers (mobile, email, business_name, owner_name, business_type, kyc_status)
         VALUES (?, ?, ?, ?, ?, ?)'
    )->execute([(string) $mobile, $mobile . '@veggiicart.test', 'Approved Fresh Hub', 'Approved Owner', 'retailer', 'approved']);
    $approved = $pdo->query('SELECT id, owner_name, business_name, kyc_status, mobile, email, created_at FROM customers ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
}

$accounts = ['pending-KYC' => $pending, 'approved-KYC' => $approved];
$profiles = [];
foreach ($accounts as $label => $row) {
    $token = JwtService::issueAccessToken((int) $row['id']);
    $p = api_get($apiBase, '/profile', $token);
    $v = api_get($apiBase, '/business/verification-status', $token);
    ok(($p['json']['success'] ?? false) === true, "$label GET /profile HTTP {$p['code']}");
    ok(($v['json']['success'] ?? false) === true, "$label GET /business/verification-status HTTP {$v['code']}");
    $data = $p['json']['data'] ?? [];
    $vdata = $v['json']['data'] ?? [];
    $profiles[$label] = $data;
    echo "  id={$data['id']} owner=" . ($data['owner_name'] ?? '') . ' business=' . ($data['business_name'] ?? '') .
        ' kyc=' . ($data['kyc_status'] ?? '') . ' mobile=' . ($data['mobile'] ?? '') . "\n";
    echo "  verify.kyc=" . ($vdata['kyc_status'] ?? '') . ' docs=' . count($vdata['documents'] ?? []) . "\n";
    ok((int) ($data['id'] ?? 0) === (int) $row['id'], "$label profile id matches JWT subject");
    ok(($data['kyc_status'] ?? '') === ($row['kyc_status'] ?? ''), "$label profile kyc_status matches DB ({$row['kyc_status']})");
    ok(($vdata['kyc_status'] ?? '') === ($data['kyc_status'] ?? ''), "$label verification-status kyc matches profile");
}

$a = $profiles['pending-KYC'] ?? [];
$b = $profiles['approved-KYC'] ?? [];
ok((int) ($a['id'] ?? 0) !== (int) ($b['id'] ?? 0), 'two different customer ids');
ok(($a['owner_name'] ?? '') !== ($b['owner_name'] ?? '') || ($a['business_name'] ?? '') !== ($b['business_name'] ?? '') || ($a['mobile'] ?? '') !== ($b['mobile'] ?? ''),
    'accounts differ on name/business/mobile');
ok(($a['kyc_status'] ?? '') !== ($b['kyc_status'] ?? ''), 'accounts differ on kyc_status (pending vs approved)');

$forbidden = ['Fresh Mart Retail', 'VC-BIZ-2026-1024', 'Rahul Sharma', 'Sam Mamgain', 'business@example.com', 'rahul@example.com'];
$pages = [
    'bussiness-profile.php', 'account-dashboard.php', 'my-profile.php', 'manage-address.php',
    'verification-status.php', 'bussiness-registration.php', 'notification.php',
    'order-success.php', 'order-details.php', 'order-details-tracking.php',
];
$web = dirname(__DIR__) . '/web';
foreach ($pages as $page) {
    $html = file_get_contents($web . '/' . $page);
    $hits = [];
    foreach ($forbidden as $needle) {
        if (str_contains($html, $needle)) {
            $hits[] = $needle;
        }
    }
    ok($hits === [], $page . ' has no dummy identity strings' . ($hits ? ' (' . implode(', ', $hits) . ')' : ''));
}

$prod1 = api_get($apiBase, '/products?per_page=3', JwtService::issueAccessToken((int) $pending['id']));
$prod2 = api_get($apiBase, '/products?per_page=3', JwtService::issueAccessToken((int) $approved['id']));
ok(($prod1['json']['success'] ?? false) && ($prod2['json']['success'] ?? false), 'GET /products works for both accounts');
$names1 = array_column($prod1['json']['data']['products'] ?? [], 'name');
$names2 = array_column($prod2['json']['data']['products'] ?? [], 'name');
ok($names1 === $names2, 'catalog product names are the same for both accounts (shared catalog)');

echo $fail === 0 ? "\nALL CHECKS PASSED\n" : "\n$fail CHECK(S) FAILED\n";
exit($fail === 0 ? 0 : 1);
