<?php
/**
 * Verify: mandatory shop-front photo, MOQ-multiple quantities (cart + orders),
 * order edit rule (max 2 edits within 120 s of placement), cancel-after-edit, and admin order address fields.
 * Usage: php scripts/verify_moq_edit.php   (needs XAMPP Apache + MySQL, SMS dev mode)
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';

spl_autoload_register(static function (string $class): void {
    foreach (['/models/', '/services/', '/core/'] as $dir) {
        $file = APP_PATH . $dir . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$fail = 0;
function check(bool $cond, string $msg): void
{
    global $fail;
    echo ($cond ? 'OK:   ' : 'FAIL: ') . $msg . "\n";
    if (!$cond) {
        $fail++;
    }
}

$apiBase = rtrim(getenv('VERIFY_API_BASE') ?: 'http://localhost/VGS/veggiicart/public/api/v1', '/');
$pdo = db();

/** @return array{code:int,json:?array,raw:string} */
function api(string $method, string $path, ?string $token = null, ?array $json = null, ?array $multipart = null): array
{
    global $apiBase;
    $ch = curl_init($apiBase . $path);
    $headers = ['Accept: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    $opts = [CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_TIMEOUT => 30];
    if ($multipart !== null) {
        $opts[CURLOPT_POSTFIELDS] = $multipart;
    } elseif ($json !== null) {
        $headers[] = 'Content-Type: application/json';
        $opts[CURLOPT_POSTFIELDS] = json_encode($json);
    }
    $opts[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $opts);
    $raw = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $decoded = json_decode($raw, true);
    return ['code' => $code, 'json' => is_array($decoded) ? $decoded : null, 'raw' => $raw];
}

function errMsg(array $r): string
{
    return (string) ($r['json']['error']['message'] ?? '');
}

function tinyPng(): string
{
    $path = sys_get_temp_dir() . '/vc_moq_' . bin2hex(random_bytes(4)) . '.png';
    file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
    return $path;
}

function stockOf(int $pid): float
{
    global $pdo;
    return (float) $pdo->query("SELECT stock FROM products WHERE id = $pid")->fetchColumn();
}

// ---------------------------------------------------------------------------
// Fresh stub customer — same state verify-otp creates, minted directly so no real SMS is sent
// ---------------------------------------------------------------------------
$mobile = '98' . str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
$customerId = (new Customer($pdo))->createFromMobile($mobile);
$token = JwtService::issueAccessToken($customerId);
check($customerId > 0 && $token !== '', "stub customer $mobile (id=$customerId) with access token");

// ---------------------------------------------------------------------------
// 1. Mandatory shop-front photo
// ---------------------------------------------------------------------------
echo "\n== Shop-front photo ==\n";
$regBody = ['business_name' => 'MOQ Verify Mart', 'owner_name' => 'MOQ Owner', 'business_type' => 'retailer'];

$r = api('POST', '/business/register', $token, $regBody);
check($r['code'] === 422 && ($r['json']['error']['code'] ?? '') === 'SHOP_PHOTO_REQUIRED',
    'register without any documents -> 422 SHOP_PHOTO_REQUIRED (' . errMsg($r) . ')');

$png = tinyPng();
$r = api('POST', '/business/documents', $token, null, [
    'document_type' => 'gst_certificate', 'file' => new CURLFile($png, 'image/png', 'gst.png'),
]);
check($r['code'] === 201, 'optional gst_certificate upload still accepted');
$r = api('POST', '/business/register', $token, $regBody);
check($r['code'] === 422 && ($r['json']['error']['code'] ?? '') === 'SHOP_PHOTO_REQUIRED',
    'register with only optional docs -> still 422 SHOP_PHOTO_REQUIRED');
$bt = (string) $pdo->query("SELECT business_type FROM customers WHERE id = $customerId")->fetchColumn();
check($bt === 'unregistered', 'customer row untouched after rejected registration');

$r = api('POST', '/business/documents', $token, null, [
    'document_type' => 'shop_front_photo', 'file' => new CURLFile($png, 'image/png', 'shop.png'),
]);
check($r['code'] === 201 && ($r['json']['data']['document_type'] ?? '') === 'business_photo',
    'shop_front_photo alias accepted, stored as business_photo');
$r = api('POST', '/business/register', $token, $regBody);
check($r['code'] === 200 && ($r['json']['success'] ?? false) === true, 'register with shop-front photo -> 200');

$vs = api('GET', '/business/verification-status', $token);
$required = array_column(array_filter($vs['json']['data']['catalog'] ?? [], static fn ($c) => !empty($c['required'])), 'key');
check($required === ['business_photo'], 'verification-status catalog marks only business_photo as required');

$pdo->prepare("UPDATE customers SET kyc_status = 'approved' WHERE id = ?")->execute([$customerId]);

// ---------------------------------------------------------------------------
// Addresses (with geo) on a serviceable pincode
// ---------------------------------------------------------------------------
$pin = (string) $pdo->query('SELECT pincode FROM serviceable_pincodes WHERE is_active = 1 LIMIT 1')->fetchColumn();
$addrIds = [];
foreach ([['Main Shop', 17.385044, 78.486671], ['Godown', 17.440081, 78.348915]] as [$label, $lat, $lng]) {
    $a = api('POST', '/addresses', $token, [
        'label' => $label, 'line1' => '12 Market Road', 'line2' => 'Near Clock Tower', 'city' => 'Hyderabad',
        'state' => 'Telangana', 'pincode' => $pin, 'landmark' => 'Opp. Bus Stand', 'geo_lat' => $lat, 'geo_lng' => $lng,
    ]);
    $addrIds[] = (int) ($a['json']['data']['address']['id'] ?? 0);
}
check($addrIds[0] > 0 && $addrIds[1] > 0, "two addresses created on serviceable pincode $pin");

// ---------------------------------------------------------------------------
// 2. MOQ multiples — two products with different MOQ
// ---------------------------------------------------------------------------
echo "\n== MOQ multiples ==\n";
$pa = $pdo->query('SELECT * FROM products WHERE is_active = 1 AND in_stock = 1 AND moq = 5 AND stock >= 60 ORDER BY id LIMIT 1')->fetch();
$pb = $pdo->query('SELECT * FROM products WHERE is_active = 1 AND in_stock = 1 AND moq = 20 AND stock >= 200 ORDER BY id LIMIT 1')->fetch();
check($pa && $pb, 'found product A (MOQ 5) and product B (MOQ 20)');
$A = (int) $pa['id'];
$B = (int) $pb['id'];
echo "      A = #$A {$pa['name']} (MOQ 5, stock {$pa['stock']}), B = #$B {$pb['name']} (MOQ 20, stock {$pb['stock']})\n";

$d = api('GET', '/products/' . $B);
check((float) ($d['json']['data']['product']['moq'] ?? 0) === 20.0
    && (float) ($d['json']['data']['product']['bulk_quote_threshold'] ?? 0) === 100.0,
    'GET /products/{id} exposes moq=20 and bulk_quote_threshold=100');
$list = api('GET', '/products?per_page=50');
$listed = array_column($list['json']['data']['products'] ?? [], null, 'id');
check(isset($listed[$A]['moq'], $listed[$A]['bulk_quote_threshold']) && (float) $listed[$A]['bulk_quote_threshold'] === 25.0,
    'GET /products exposes moq and bulk_quote_threshold (A: 25)');

$r = api('POST', '/cart/items', $token, ['product_id' => $A, 'quantity' => 7, 'replace' => true]);
check($r['code'] === 422 && str_contains(errMsg($r), 'MOQ of 5'), 'cart add A qty 7 -> 422: ' . errMsg($r));
$r = api('POST', '/cart/items', $token, ['product_id' => $A, 'quantity' => 2.5, 'replace' => true]);
check($r['code'] === 422, 'cart add A qty 2.5 (below MOQ) -> 422');
$r = api('POST', '/cart/items', $token, ['product_id' => $A, 'quantity' => 10, 'replace' => true]);
check($r['code'] === 201, 'cart add A qty 10 -> 201');
$cartA = (int) ($r['json']['data']['added_item_id'] ?? 0);

$r = api('POST', '/cart/items', $token, ['product_id' => $B, 'quantity' => 30, 'replace' => true]);
check($r['code'] === 422 && str_contains(errMsg($r), 'MOQ of 20'), 'cart add B qty 30 -> 422: ' . errMsg($r));
$r = api('POST', '/cart/items', $token, ['product_id' => $B, 'quantity' => 40, 'replace' => true]);
check($r['code'] === 201, 'cart add B qty 40 -> 201');
$cartLines = array_column($r['json']['data']['items'] ?? [], null, 'product_id');
check((float) ($cartLines[$B]['bulk_quote_threshold'] ?? 0) === 100.0, 'cart line exposes bulk_quote_threshold');

$r = api('PUT', '/cart/items/' . $cartA, $token, ['quantity' => 12]);
check($r['code'] === 422 && str_contains(errMsg($r), 'MOQ of 5'), 'cart PUT A qty 12 -> 422');
$r = api('PUT', '/cart/items/' . $cartA, $token, ['quantity' => 15]);
check($r['code'] === 200, 'cart PUT A qty 15 -> 200');

// Multi-address: B split 25 + 15 is not MOQ-aligned
$r = api('POST', '/orders/multi-address', $token, ['addresses' => [
    ['address_id' => $addrIds[0], 'items' => [['product_id' => $A, 'quantity' => 5], ['product_id' => $B, 'quantity' => 25]]],
    ['address_id' => $addrIds[1], 'items' => [['product_id' => $A, 'quantity' => 10], ['product_id' => $B, 'quantity' => 15]]],
]]);
check($r['code'] === 422 && str_contains(errMsg($r), 'MOQ of 20'), 'multi-address B split 25+15 -> 422: ' . errMsg($r));

$r = api('POST', '/orders/multi-address', $token, ['addresses' => [
    ['address_id' => $addrIds[0], 'items' => [['product_id' => $A, 'quantity' => 5], ['product_id' => $B, 'quantity' => 20]]],
    ['address_id' => $addrIds[1], 'items' => [['product_id' => $A, 'quantity' => 10], ['product_id' => $B, 'quantity' => 20]]],
]]);
check($r['code'] === 201 && count($r['json']['data']['orders'] ?? []) === 2, 'multi-address A 5+10, B 20+20 -> 201');
foreach ($r['json']['data']['orders'] ?? [] as $o) {
    api('POST', '/orders/' . $o['order_id'] . '/cancel', $token, ['reason' => 'verify cleanup']);
}

// Single checkout: legacy non-multiple quantity sitting in the cart is rejected at POST /orders
api('POST', '/cart/items', $token, ['product_id' => $A, 'quantity' => 15, 'replace' => true]);
api('POST', '/cart/items', $token, ['product_id' => $B, 'quantity' => 40, 'replace' => true]);
$pdo->prepare('UPDATE cart_items SET quantity = 25 WHERE customer_id = ? AND product_id = ?')->execute([$customerId, $B]);
$r = api('POST', '/orders', $token, ['address_id' => $addrIds[0]]);
check($r['code'] === 422 && str_contains(errMsg($r), 'MOQ of 20'), 'POST /orders with B=25 in cart -> 422: ' . errMsg($r));
$pdo->prepare('UPDATE cart_items SET quantity = 40 WHERE customer_id = ? AND product_id = ?')->execute([$customerId, $B]);

$r = api('POST', '/orders', $token, ['address_id' => $addrIds[0]]);
check($r['code'] === 201, 'POST /orders with A=15, B=40 -> 201');
$orderId = (int) ($r['json']['data']['order']['id'] ?? 0);
$new = $r['json']['data']['order'] ?? [];
check(($new['edit_count'] ?? null) === 0 && ($new['can_edit'] ?? null) === true && ($new['edit_remaining'] ?? null) === 2,
    'new order: edit_count=0, can_edit=true, edit_remaining=2');

// ---------------------------------------------------------------------------
// 5. Edit rule: max 2 edits within 120 s of placement (confirmed order so stock re-balancing is exercised)
// ---------------------------------------------------------------------------
echo "\n== Order edit (max " . Order::ORDER_EDIT_MAX_COUNT . ' within ' . Order::ORDER_EDIT_WINDOW_SECONDS . "s) ==\n";
check(Order::ORDER_EDIT_WINDOW_SECONDS === 120 && Order::ORDER_EDIT_MAX_COUNT === 2, 'constants: window 120s, max 2 edits');

$g = api('GET', '/orders/' . $orderId, $token)['json']['data']['order'] ?? [];
$placedAt = (string) $pdo->query("SELECT placed_at FROM orders WHERE id = $orderId")->fetchColumn();
$expiresTs = strtotime((string) ($g['edit_expires_at'] ?? ''));
check($expiresTs === strtotime($placedAt) + 120, "GET /orders/{id}: edit_expires_at = placed_at + 120s ({$g['edit_expires_at']})");
$left = $expiresTs - time();
check($left > 90 && $left <= 120 && ($g['edit_seconds_left'] ?? -1) > 90 && ($g['edit_seconds_left'] ?? 999) <= 120,
    "timezone consistent: PHP sees ~{$left}s left on a just-placed order (DB placed_at $placedAt)");
check(isset($g['server_time']) && abs(strtotime($g['server_time']) - time()) <= 5, 'server_time present and close to now');
$dbPhpSkew = abs((int) $pdo->query('SELECT UNIX_TIMESTAMP(NOW())')->fetchColumn() - strtotime((string) $pdo->query('SELECT NOW()')->fetchColumn()));
check($dbPhpSkew <= 1, 'MySQL NOW() and PHP strtotime agree on the same instant (session time_zone aligned)');
$adminId = (int) $pdo->query("SELECT id FROM admin_users WHERE role_type = 'super_admin' LIMIT 1")->fetchColumn();
$stockA0 = stockOf($A);
$stockB0 = stockOf($B);
(new OrderService($pdo))->changeStatus($orderId, 'confirmed', $adminId);
check(abs(stockOf($A) - ($stockA0 - 15)) < 0.001 && abs(stockOf($B) - ($stockB0 - 40)) < 0.001, 'confirm deducted A15/B40');

$r = api('PUT', '/orders/' . $orderId, $token, ['items' => [['product_id' => $A, 'quantity' => 9]]]);
check($r['code'] === 422 && str_contains(errMsg($r), 'MOQ of 5'), 'edit with A qty 9 -> 422: ' . errMsg($r));
check((int) $pdo->query("SELECT edit_count FROM orders WHERE id = $orderId")->fetchColumn() === 0, 'rejected edit leaves edit_count=0');
check(abs(stockOf($A) - ($stockA0 - 15)) < 0.001, 'rejected edit leaves stock untouched (rolled back)');

$r = api('PUT', '/orders/' . $orderId, $token, ['items' => []]);
check($r['code'] === 422, 'edit with empty items -> 422');

$priceA = (float) $pa['price'];
$priceB = (float) $pb['price'];
$fee = (float) $pdo->query("SELECT delivery_fee FROM orders WHERE id = $orderId")->fetchColumn();
$r = api('PUT', '/orders/' . $orderId, $token, ['items' => [
    ['product_id' => $A, 'quantity' => 5],
    ['product_id' => $B, 'quantity' => 60],
]]);
$o = $r['json']['data']['order'] ?? [];
check($r['code'] === 200, 'first edit A 15->5, B 40->60 -> 200');
check(($o['edit_count'] ?? null) === 1 && ($o['edit_remaining'] ?? null) === 1 && ($o['can_edit'] ?? null) === true
    && ($o['can_cancel'] ?? null) === true,
    'after first edit: edit_count=1, edit_remaining=1, can_edit=true, can_cancel=true');
check(($r['json']['data']['message'] ?? '') === 'Order updated. You can edit this order 1 more time(s) within the remaining time.',
    'first edit message: ' . ($r['json']['data']['message'] ?? ''));
$expectedSubtotal = round(5 * $priceA + 60 * $priceB, 2);
check(abs((float) ($o['subtotal'] ?? 0) - $expectedSubtotal) < 0.01
    && abs((float) ($o['total'] ?? 0) - round($expectedSubtotal + $fee, 2)) < 0.01,
    "totals recalculated (subtotal $expectedSubtotal + fee $fee)");
$items = $pdo->query("SELECT product_id, quantity FROM order_items WHERE order_id = $orderId ORDER BY product_id")->fetchAll(PDO::FETCH_KEY_PAIR);
check(count($items) === 2 && (float) $items[$A] === 5.0 && (float) $items[$B] === 60.0, 'order_items replaced in DB');
check(abs(stockOf($A) - ($stockA0 - 5)) < 0.001 && abs(stockOf($B) - ($stockB0 - 60)) < 0.001,
    'stock re-balanced for confirmed order (A back +10, B extra -20)');

$r = api('PUT', '/orders/' . $orderId, $token, ['items' => [
    ['product_id' => $A, 'quantity' => 10],
    ['product_id' => $B, 'quantity' => 60],
]]);
$o = $r['json']['data']['order'] ?? [];
check($r['code'] === 200, 'second edit A 5->10 -> 200');
check(($o['edit_count'] ?? null) === 2 && ($o['edit_remaining'] ?? null) === 0 && ($o['can_edit'] ?? null) === false
    && ($o['edit_seconds_left'] ?? null) === 0 && ($o['can_cancel'] ?? null) === true,
    'after second edit: edit_count=2, edit_remaining=0, can_edit=false, can_cancel=true');
check(($r['json']['data']['message'] ?? '') === 'Your order has been confirmed. This order cannot be modified again.',
    'second edit message: ' . ($r['json']['data']['message'] ?? ''));
check(abs(stockOf($A) - ($stockA0 - 10)) < 0.001 && abs(stockOf($B) - ($stockB0 - 60)) < 0.001,
    'stock re-balanced again (A -5 more)');

$r = api('PUT', '/orders/' . $orderId, $token, ['items' => [['product_id' => $A, 'quantity' => 15]]]);
check($r['code'] === 422 && ($r['json']['error']['code'] ?? '') === 'EDIT_LIMIT_REACHED',
    'third edit (PUT) -> 422 EDIT_LIMIT_REACHED: ' . errMsg($r));
$r = api('POST', '/orders/' . $orderId, $token, ['items' => [['product_id' => $A, 'quantity' => 15]]]);
check($r['code'] === 422 && ($r['json']['error']['code'] ?? '') === 'EDIT_LIMIT_REACHED', 'third edit (POST fallback) -> 422 EDIT_LIMIT_REACHED');
try {
    (new CheckoutService($pdo))->editOrder($orderId, $customerId, [['product_id' => $A, 'quantity' => 15]]);
    check(false, 'service-level third edit rejected');
} catch (OrderEditDeniedException $e) {
    check($e->errorCode === 'EDIT_LIMIT_REACHED', 'service-level third edit rejected with EDIT_LIMIT_REACHED (bypassing controller)');
}
check((int) $pdo->query("SELECT edit_count FROM orders WHERE id = $orderId")->fetchColumn() === 2
    && abs(stockOf($A) - ($stockA0 - 10)) < 0.001, 'rejected third edit leaves edit_count=2 and stock untouched');
$g = api('GET', '/orders/' . $orderId, $token)['json']['data']['order'] ?? [];
check(($g['can_edit'] ?? null) === false && ($g['edit_count'] ?? null) === 2 && ($g['edit_remaining'] ?? null) === 0,
    'GET /orders/{id} after limit: can_edit=false, edit_count=2, edit_remaining=0');

// ---------------------------------------------------------------------------
// 6. Cancel after edits (independent of edit_count / window)
// ---------------------------------------------------------------------------
echo "\n== Cancel after edits ==\n";
$r = api('POST', '/orders/' . $orderId . '/cancel', $token, ['reason' => 'verify cancel after edit']);
check($r['code'] === 200 && ($r['json']['data']['order']['status'] ?? '') === 'cancelled', 'cancel twice-edited order -> 200 cancelled');
check(abs(stockOf($A) - $stockA0) < 0.001 && abs(stockOf($B) - $stockB0) < 0.001, 'cancel restored the edited quantities exactly');

$cartCount = (int) $pdo->query("SELECT COUNT(*) FROM cart_items WHERE customer_id = $customerId")->fetchColumn();
check($cartCount === 0, 'cart empty after place/edit/cancel (edit never touched cart)');

// ---------------------------------------------------------------------------
// 7. Edit window (placed_at moved back on the DB clock instead of sleeping 2 minutes)
// ---------------------------------------------------------------------------
echo "\n== Edit window ==\n";
api('POST', '/cart/items', $token, ['product_id' => $A, 'quantity' => 5, 'replace' => true]);
$r = api('POST', '/orders', $token, ['address_id' => $addrIds[1]]);
$fresh = $r['json']['data']['order'] ?? [];
check($r['code'] === 201 && ($fresh['edit_count'] ?? null) === 0 && ($fresh['can_edit'] ?? null) === true,
    'fresh order -> 201, edit_count=0, can_edit=true');
$freshId = (int) ($fresh['id'] ?? 0);

$pdo->exec("UPDATE orders SET placed_at = NOW() - INTERVAL 110 SECOND WHERE id = $freshId");
$stockA1 = stockOf($A);
$r = api('PUT', '/orders/' . $freshId, $token, ['items' => [['product_id' => $A, 'quantity' => 10]]]);
check($r['code'] === 200 && ($r['json']['data']['order']['edit_count'] ?? null) === 1
    && abs(stockOf($A) - $stockA1) < 0.001, 'edit at 110s (inside window) on placed order -> 200, no stock movement until confirm');
$sl = (int) ($r['json']['data']['order']['edit_seconds_left'] ?? -1);
check($sl >= 1 && $sl <= 10, "edit_seconds_left reflects the shrinking window ({$sl}s)");

$pdo->exec("UPDATE orders SET placed_at = NOW() - INTERVAL 121 SECOND WHERE id = $freshId");
$g = api('GET', '/orders/' . $freshId, $token)['json']['data']['order'] ?? [];
check(($g['can_edit'] ?? null) === false && ($g['edit_seconds_left'] ?? null) === 0 && ($g['edit_remaining'] ?? null) === 1,
    'GET at 121s: can_edit=false, edit_seconds_left=0, edit_remaining=1');
$r = api('PUT', '/orders/' . $freshId, $token, ['items' => [['product_id' => $A, 'quantity' => 15]]]);
check($r['code'] === 422 && ($r['json']['error']['code'] ?? '') === 'EDIT_WINDOW_EXPIRED',
    'edit at 121s -> 422 EDIT_WINDOW_EXPIRED: ' . errMsg($r));
check((int) $pdo->query("SELECT edit_count FROM orders WHERE id = $freshId")->fetchColumn() === 1,
    'expired-window edit leaves edit_count=1');
$r = api('POST', '/orders/' . $freshId . '/cancel', $token, ['reason' => 'verify cleanup']);
check($r['code'] === 200, 'cancel after the edit window -> 200 (cancel is status-based only)');

$list = api('GET', '/orders', $token);
$row = array_values(array_filter($list['json']['data']['orders'] ?? [], static fn ($o) => (int) $o['id'] === $orderId))[0] ?? [];
check(($row['edit_count'] ?? null) === 2 && ($row['can_edit'] ?? null) === false && ($row['edit_remaining'] ?? null) === 0
    && array_key_exists('edit_expires_at', $row), 'GET /orders list exposes edit_count/can_edit/edit_remaining/edit_expires_at');

// ---------------------------------------------------------------------------
// Admin order detail address fields (Order::find feeds /orders/{id} admin page)
// ---------------------------------------------------------------------------
echo "\n== Admin order address data ==\n";
$admin = (new Order($pdo))->find($orderId);
$keys = ['address_id', 'address_label', 'line1', 'line2', 'city', 'state', 'pincode', 'landmark', 'geo_lat', 'geo_lng'];
check(array_diff($keys, array_keys($admin ?? [])) === [], 'Order::find has ' . implode(', ', $keys));
check(abs((float) $admin['geo_lat'] - 17.385044) < 0.00001 && abs((float) $admin['geo_lng'] - 78.486671) < 0.00001,
    "geo values present ({$admin['geo_lat']}, {$admin['geo_lng']})");
echo '      sample: ' . json_encode(array_intersect_key($admin, array_flip($keys))) . "\n";

@unlink($png);

// ---------------------------------------------------------------------------
// Cleanup: every order above is cancelled (stock restored), so drop the stub customer and its data.
// Set VERIFY_KEEP_DATA=1 to keep it for inspection.
// ---------------------------------------------------------------------------
if (!getenv('VERIFY_KEEP_DATA')) {
    $open = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE customer_id = $customerId AND status <> 'cancelled'")->fetchColumn();
    if ($open === 0) {
        $pdo->exec("DELETE FROM orders WHERE customer_id = $customerId");
        $pdo->exec("DELETE FROM customers WHERE id = $customerId");
        $dir = PUBLIC_PATH . '/uploads/kyc/' . $customerId;
        foreach (glob($dir . '/*') ?: [] as $f) {
            @unlink($f);
        }
        @rmdir($dir);
        echo "\n      cleaned up stub customer $customerId and its orders\n";
    } else {
        echo "\n      kept stub customer $customerId ($open open order(s))\n";
    }
}

echo "\n" . ($fail === 0 ? 'All MOQ / edit / shop-photo checks passed.' : "$fail check(s) failed.") . "\n";
exit($fail === 0 ? 0 : 1);
