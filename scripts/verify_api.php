<?php
/**
 * Full customer API verify — core P1 flow + every previously "BUILT BUT UNTESTED" endpoint.
 * Usage: php scripts/verify_api.php
 *
 * Defaults to HTTP against VERIFY_API_BASE (or local XAMPP). Also checks DB side-effects.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';
require_once dirname(__DIR__) . '/app/middleware/api_auth.php';

spl_autoload_register(static function (string $class): void {
    foreach ([
        APP_PATH . '/controllers/api/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
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
function assert_true(bool $cond, string $msg): void
{
    global $fail;
    if (!$cond) {
        echo "FAIL: $msg\n";
        $fail++;
        return;
    }
    echo "OK: $msg\n";
}

$apiBase = rtrim(getenv('VERIFY_API_BASE') ?: 'http://localhost/VGS/veggiicart/public/api/v1', '/');
$pdo = db();

/**
 * @param array<string,mixed>|null $json
 * @param array<string,mixed>|null $multipart  field => value|CURLFile
 * @return array{code:int,json:?array,raw:string}
 */
function api(string $method, string $path, ?string $token = null, ?array $json = null, ?array $multipart = null): array
{
    global $apiBase;
    $url = $apiBase . $path;
    $ch = curl_init($url);
    $headers = ['Accept: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => $headers,
    ];

    if ($multipart !== null) {
        $opts[CURLOPT_POSTFIELDS] = $multipart;
    } elseif ($json !== null) {
        $headers[] = 'Content-Type: application/json';
        $opts[CURLOPT_HTTPHEADER] = $headers;
        $opts[CURLOPT_POSTFIELDS] = json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    curl_setopt_array($ch, $opts);
    $raw = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err !== '') {
        return ['code' => 0, 'json' => null, 'raw' => $err];
    }
    $decoded = json_decode($raw, true);
    return ['code' => $code, 'json' => is_array($decoded) ? $decoded : null, 'raw' => $raw];
}

function makeTinyPng(): string
{
    $path = sys_get_temp_dir() . '/vc_verify_' . bin2hex(random_bytes(4)) . '.png';
    // 1x1 PNG
    $bin = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    file_put_contents($path, $bin);
    return $path;
}

// ---------------------------------------------------------------------------
// Bootstrap customer via OTP (HTTP)
// ---------------------------------------------------------------------------
$mobile = '9876599999';
$pdo->prepare('DELETE FROM otp_rate_limits WHERE mobile = ?')->execute([$mobile]);
$pdo->prepare('DELETE FROM otp_codes WHERE mobile = ?')->execute([$mobile]);

$tables = ['otp_codes', 'refresh_tokens', 'cart_items', 'delivery_slots', 'cart_meta', 'faqs'];
foreach ($tables as $t) {
    assert_true((bool) $pdo->query("SHOW TABLES LIKE '$t'")->fetchColumn(), "table $t exists");
}

$send = api('POST', '/auth/send-otp', null, ['mobile' => $mobile]);
assert_true(($send['json']['success'] ?? false) === true, 'POST /auth/send-otp');
$otp = (string) ($send['json']['data']['dev_otp'] ?? '');
assert_true($otp !== '' && strlen($otp) === 6, 'send-otp returns dev_otp in DEV MODE');
assert_true(!empty($send['json']['data']['dev_mode']), 'SMS DEV MODE flag present');

// resend-otp
$pdo->prepare('DELETE FROM otp_rate_limits WHERE mobile = ?')->execute([$mobile]);
$resend = api('POST', '/auth/resend-otp', null, ['mobile' => $mobile]);
assert_true(($resend['json']['success'] ?? false) === true, 'POST /auth/resend-otp');
$otp = (string) ($resend['json']['data']['dev_otp'] ?? $otp);

$verify = api('POST', '/auth/verify-otp', null, ['mobile' => $mobile, 'otp' => $otp]);
assert_true(($verify['json']['success'] ?? false) === true, 'POST /auth/verify-otp');
$access = (string) ($verify['json']['data']['access_token'] ?? '');
$refresh = (string) ($verify['json']['data']['refresh_token'] ?? '');
assert_true($access !== '' && $refresh !== '', 'tokens issued');
$customerId = (int) ($verify['json']['data']['customer']['id'] ?? 0);
assert_true($customerId > 0, "customer id=$customerId");

// Ensure stock + clean cart/wishlist for this customer
$pdo->prepare('DELETE FROM cart_items WHERE customer_id = ?')->execute([$customerId]);
$pdo->prepare('DELETE FROM wishlists WHERE customer_id = ?')->execute([$customerId]);
$pdo->prepare('DELETE FROM cart_meta WHERE customer_id = ?')->execute([$customerId]);

$email = 'api.verify.' . $customerId . '@veggiicart.test';
$password = 'VerifyApi@123';
$pdo->prepare('UPDATE customers SET email = ?, password_hash = ? WHERE id = ?')
    ->execute([$email, password_hash($password, PASSWORD_DEFAULT), $customerId]);

// ---------------------------------------------------------------------------
// Auth extras: email-login, refresh-token, logout
// ---------------------------------------------------------------------------
$emailLogin = api('POST', '/auth/email-login', null, ['email' => $email, 'password' => $password]);
assert_true(($emailLogin['json']['success'] ?? false) === true, 'POST /auth/email-login');
assert_true(!empty($emailLogin['json']['data']['access_token']), 'email-login returns access_token');
$access = (string) $emailLogin['json']['data']['access_token'];
$refresh = (string) ($emailLogin['json']['data']['refresh_token'] ?? $refresh);

$beforeRefreshHash = $pdo->prepare('SELECT token_hash FROM refresh_tokens WHERE customer_id = ? AND revoked_at IS NULL ORDER BY id DESC LIMIT 1');
$beforeRefreshHash->execute([$customerId]);
$oldHash = $beforeRefreshHash->fetchColumn();

$refreshed = api('POST', '/auth/refresh-token', null, ['refresh_token' => $refresh]);
assert_true(($refreshed['json']['success'] ?? false) === true, 'POST /auth/refresh-token');
$newRefresh = (string) ($refreshed['json']['data']['refresh_token'] ?? '');
$access = (string) ($refreshed['json']['data']['access_token'] ?? $access);
assert_true($newRefresh !== '' && $newRefresh !== $refresh, 'refresh token rotated');

$revoked = $pdo->prepare('SELECT revoked_at FROM refresh_tokens WHERE token_hash = ?');
$revoked->execute([hash('sha256', $refresh)]);
// JwtService::hashToken may differ — check old row revoked via find
$oldRow = $pdo->prepare('SELECT revoked_at FROM refresh_tokens WHERE customer_id = ? AND token_hash = ?');
// Use same hash helper
$oldTokenHash = null;
if (class_exists('JwtService')) {
    $oldTokenHash = JwtService::hashToken($refresh);
}
if ($oldTokenHash) {
    $chk = $pdo->prepare('SELECT revoked_at IS NOT NULL FROM refresh_tokens WHERE token_hash = ?');
    $chk->execute([$oldTokenHash]);
    assert_true((int) $chk->fetchColumn() === 1, 'old refresh token revoked in DB');
}

$logoutRefresh = $newRefresh;
$logout = api('POST', '/auth/logout', $access, ['refresh_token' => $logoutRefresh]);
assert_true(($logout['json']['success'] ?? false) === true, 'POST /auth/logout');
if ($logoutRefresh !== '') {
    $lh = JwtService::hashToken($logoutRefresh);
    $chk = $pdo->prepare('SELECT revoked_at IS NOT NULL FROM refresh_tokens WHERE token_hash = ?');
    $chk->execute([$lh]);
    assert_true((int) $chk->fetchColumn() === 1, 'logout revoked refresh token in DB');
}

// Re-login for remaining tests
$pdo->prepare('DELETE FROM otp_rate_limits WHERE mobile = ?')->execute([$mobile]);
$send = api('POST', '/auth/send-otp', null, ['mobile' => $mobile]);
$otp = (string) ($send['json']['data']['dev_otp'] ?? '');
$verify = api('POST', '/auth/verify-otp', null, ['mobile' => $mobile, 'otp' => $otp]);
$access = (string) ($verify['json']['data']['access_token'] ?? '');
$refreshKeep = (string) ($verify['json']['data']['refresh_token'] ?? '');
assert_true($access !== '', 're-auth after logout');

// ---------------------------------------------------------------------------
// Business types + register + documents + resubmit
// ---------------------------------------------------------------------------
$bt = api('GET', '/business-types');
assert_true(($bt['json']['success'] ?? false) === true && !empty($bt['json']['data']['business_types']), 'GET /business-types');

$reg = api('POST', '/business/register', $access, [
    'business_name' => 'Verify API Mart',
    'owner_name'    => 'Verify Owner',
    'business_type' => 'retailer',
    'gst_number'    => '27AAAAA0000A1Z5',
    'email'         => $email,
]);
assert_true(($reg['json']['success'] ?? false) === true, 'POST /business/register');
$row = $pdo->query("SELECT business_name, kyc_status FROM customers WHERE id=$customerId")->fetch();
assert_true(($row['business_name'] ?? '') === 'Verify API Mart' && ($row['kyc_status'] ?? '') === 'pending', 'register updated DB (pending KYC)');

$png = makeTinyPng();
$doc = api('POST', '/business/documents', $access, null, [
    'document_type' => 'gst_certificate',
    'file'          => new CURLFile($png, 'image/png', 'gst.png'),
]);
assert_true(($doc['json']['success'] ?? false) === true || ($doc['code'] === 201 && ($doc['json']['success'] ?? false)), 'POST /business/documents');
$docId = (int) ($doc['json']['data']['id'] ?? 0);
assert_true($docId > 0, "document id=$docId persisted");
$dbDoc = (int) $pdo->query("SELECT COUNT(*) FROM customer_documents WHERE id=$docId AND customer_id=$customerId")->fetchColumn();
assert_true($dbDoc === 1, 'document row in DB');

$listDocs = api('GET', '/business/documents', $access);
assert_true(($listDocs['json']['success'] ?? false) === true && count($listDocs['json']['data']['documents'] ?? []) >= 1, 'GET /business/documents');

$pdo->prepare("UPDATE customers SET kyc_status='rejected', kyc_rejection_reason='verify reject' WHERE id=?")
    ->execute([$customerId]);
$resub = api('POST', '/business/resubmit', $access, ['business_name' => 'Verify API Mart Resubmit']);
assert_true(($resub['json']['success'] ?? false) === true, 'POST /business/resubmit');
$kyc = $pdo->query("SELECT kyc_status, business_name FROM customers WHERE id=$customerId")->fetch();
assert_true(($kyc['kyc_status'] ?? '') === 'pending', 'resubmit set kyc_status=pending');
assert_true(($kyc['business_name'] ?? '') === 'Verify API Mart Resubmit', 'resubmit updated business_name');

// ---------------------------------------------------------------------------
// Profile PUT + avatar
// ---------------------------------------------------------------------------
$prof = api('PUT', '/profile', $access, ['owner_name' => 'Verify Owner Updated']);
assert_true(($prof['json']['success'] ?? false) === true, 'PUT /profile');
assert_true(($prof['json']['data']['owner_name'] ?? '') === 'Verify Owner Updated', 'profile response owner_name');
assert_true(
    $pdo->query("SELECT owner_name FROM customers WHERE id=$customerId")->fetchColumn() === 'Verify Owner Updated',
    'profile owner_name in DB'
);

$profPost = api('POST', '/profile', $access, ['owner_name' => 'Verify Owner Post']);
assert_true(($profPost['json']['success'] ?? false) === true, 'POST /profile (PUT fallback)');
assert_true(
    $pdo->query("SELECT owner_name FROM customers WHERE id=$customerId")->fetchColumn() === 'Verify Owner Post',
    'POST profile updated DB'
);

$png2 = makeTinyPng();
$av = api('POST', '/profile/avatar', $access, null, [
    'avatar' => new CURLFile($png2, 'image/png', 'avatar.png'),
]);
assert_true(($av['json']['success'] ?? false) === true, 'POST /profile/avatar');
$avatarUrl = (string) ($av['json']['data']['avatar_url'] ?? '');
assert_true($avatarUrl !== '', 'avatar_url returned');
$dbAvatar = (string) $pdo->query("SELECT avatar_url FROM customers WHERE id=$customerId")->fetchColumn();
assert_true($dbAvatar !== '' && $dbAvatar !== null, 'avatar_url stored in DB');

$avDel = api('DELETE', '/profile/avatar', $access);
assert_true(($avDel['json']['success'] ?? false) === true, 'DELETE /profile/avatar');
assert_true(
    $pdo->query("SELECT avatar_url FROM customers WHERE id=$customerId")->fetchColumn() === null
    || $pdo->query("SELECT avatar_url FROM customers WHERE id=$customerId")->fetchColumn() === '',
    'avatar cleared in DB'
);

// ---------------------------------------------------------------------------
// Addresses CRUD + default
// ---------------------------------------------------------------------------
$addrCreate = api('POST', '/addresses', $access, [
    'label' => 'Verify Shop',
    'line1' => '42 Verify Lane',
    'city' => 'Pune',
    'state' => 'Maharashtra',
    'pincode' => '411001',
    'is_default' => false,
]);
assert_true(($addrCreate['code'] === 201 || ($addrCreate['json']['success'] ?? false)), 'POST /addresses');
$addrId = (int) ($addrCreate['json']['data']['address']['id'] ?? 0);
assert_true($addrId > 0, "address id=$addrId");
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM addresses WHERE id=$addrId AND customer_id=$customerId")->fetchColumn() === 1,
    'address row in DB'
);

$addrUp = api('PUT', '/addresses/' . $addrId, $access, [
    'line1' => '42 Verify Lane Updated',
    'city' => 'Pune',
    'state' => 'Maharashtra',
    'pincode' => '411002',
]);
assert_true(($addrUp['json']['success'] ?? false) === true, 'PUT /addresses/{id}');
assert_true(
    $pdo->query("SELECT line1 FROM addresses WHERE id=$addrId")->fetchColumn() === '42 Verify Lane Updated',
    'address line1 updated in DB'
);

$addrDef = api('POST', '/addresses/' . $addrId . '/default', $access);
assert_true(($addrDef['json']['success'] ?? false) === true, 'POST /addresses/{id}/default');
assert_true(
    (int) $pdo->query("SELECT is_default FROM addresses WHERE id=$addrId")->fetchColumn() === 1,
    'address marked default in DB'
);

// Keep a second address for delete test
$addr2 = api('POST', '/addresses', $access, [
    'label' => 'Temp Delete',
    'line1' => 'Temp Road',
    'city' => 'Pune',
    'state' => 'Maharashtra',
    'pincode' => '411003',
]);
$addr2Id = (int) ($addr2['json']['data']['address']['id'] ?? 0);
$addrDel = api('DELETE', '/addresses/' . $addr2Id, $access);
assert_true(($addrDel['json']['success'] ?? false) === true, 'DELETE /addresses/{id}');
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM addresses WHERE id=$addr2Id")->fetchColumn() === 0,
    'address deleted from DB'
);

// ---------------------------------------------------------------------------
// Catalog: search + product detail
// ---------------------------------------------------------------------------
$search = api('GET', '/products/search?q=Tomato');
assert_true(($search['json']['success'] ?? false) === true, 'GET /products/search');
$foundTomato = false;
foreach ($search['json']['data']['products'] ?? $search['json']['data']['items'] ?? [] as $p) {
    if (stripos((string) ($p['name'] ?? ''), 'Tomato') !== false) {
        $foundTomato = true;
        break;
    }
}
// Controllers may nest under products key — also accept any row
if (!$foundTomato) {
    $raw = $search['raw'];
    $foundTomato = str_contains($raw, 'Tomato');
}
assert_true($foundTomato, 'search returns Tomato');

$productRow = $pdo->query('SELECT id, moq, stock, price, name FROM products WHERE is_active=1 AND stock >= 30 ORDER BY id LIMIT 1')->fetch();
$productId = (int) $productRow['id'];
$detail = api('GET', '/products/' . $productId);
assert_true(($detail['json']['success'] ?? false) === true, 'GET /products/{id}');
assert_true((int) ($detail['json']['data']['product']['id'] ?? $detail['json']['data']['id'] ?? 0) === $productId
    || str_contains($detail['raw'], '"id":' . $productId)
    || str_contains($detail['raw'], '"id": ' . $productId), 'product detail matches id');

// ---------------------------------------------------------------------------
// Cart: add, update, coupon apply/remove, delete item
// ---------------------------------------------------------------------------
$moq = max(1.0, (float) $productRow['moq']);
$qty = max($moq, 2.0);
$add = api('POST', '/cart/items', $access, [
    'product_id' => $productId,
    'quantity'   => $qty,
    'replace'    => true,
]);
assert_true(($add['json']['success'] ?? false) === true, 'POST /cart/items');
$cartItemId = (int) ($add['json']['data']['added_item_id'] ?? 0);
if ($cartItemId < 1 && !empty($add['json']['data']['items'][0]['id'])) {
    $cartItemId = (int) $add['json']['data']['items'][0]['id'];
}
assert_true($cartItemId > 0, "cart item id=$cartItemId");
assert_true(
    (float) $pdo->query("SELECT quantity FROM cart_items WHERE id=$cartItemId")->fetchColumn() === $qty,
    'cart quantity in DB after add'
);

$newQty = $qty + $moq;
$upd = api('PUT', '/cart/items/' . $cartItemId, $access, ['quantity' => $newQty]);
assert_true(($upd['json']['success'] ?? false) === true, 'PUT /cart/items/{id}');
assert_true(
    abs((float) $pdo->query("SELECT quantity FROM cart_items WHERE id=$cartItemId")->fetchColumn() - $newQty) < 0.001,
    'cart quantity updated in DB'
);

$coupon = api('POST', '/cart/coupon', $access, ['coupon_code' => 'FLAT50']);
assert_true(($coupon['json']['success'] ?? false) === true, 'POST /cart/coupon (FLAT50)');
assert_true(
    strtoupper((string) $pdo->query("SELECT coupon_code FROM cart_meta WHERE customer_id=$customerId")->fetchColumn()) === 'FLAT50',
    'coupon stored in cart_meta'
);

$couponDel = api('DELETE', '/cart/coupon', $access);
assert_true(($couponDel['json']['success'] ?? false) === true, 'DELETE /cart/coupon');
$codeAfter = $pdo->query("SELECT coupon_code FROM cart_meta WHERE customer_id=$customerId")->fetchColumn();
assert_true($codeAfter === null || $codeAfter === '', 'coupon cleared from cart_meta');

// Soft-delete path for cart item: remove then re-add for checkout
$rm = api('DELETE', '/cart/items/' . $cartItemId, $access);
assert_true(($rm['json']['success'] ?? false) === true, 'DELETE /cart/items/{id}');
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM cart_items WHERE id=$cartItemId")->fetchColumn() === 0,
    'cart item removed from DB'
);

$add = api('POST', '/cart/items', $access, [
    'product_id' => $productId,
    'quantity'   => $qty,
    'replace'    => true,
]);
assert_true(($add['json']['success'] ?? false) === true, 're-add cart item for checkout');

// ---------------------------------------------------------------------------
// Wishlist: add, move-to-cart, delete
// ---------------------------------------------------------------------------
$wlProduct = $pdo->query("SELECT id, moq, stock FROM products WHERE is_active=1 AND id <> $productId AND stock >= 20 ORDER BY id LIMIT 1")->fetch();
$wlPid = (int) $wlProduct['id'];
$wlAdd = api('POST', '/wishlist', $access, ['product_id' => $wlPid]);
assert_true(($wlAdd['json']['success'] ?? false) === true || $wlAdd['code'] === 201, 'POST /wishlist');
$wlId = (int) ($wlAdd['json']['data']['id'] ?? 0);
if ($wlId < 1) {
    $wlId = (int) $pdo->query("SELECT id FROM wishlists WHERE customer_id=$customerId AND product_id=$wlPid")->fetchColumn();
}
assert_true($wlId > 0, "wishlist id=$wlId");

$move = api('POST', '/wishlist/' . $wlId . '/move-to-cart', $access);
assert_true(($move['json']['success'] ?? false) === true, 'POST /wishlist/{id}/move-to-cart');
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM wishlists WHERE id=$wlId")->fetchColumn() === 0,
    'wishlist item removed after move-to-cart'
);
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM cart_items WHERE customer_id=$customerId AND product_id=$wlPid")->fetchColumn() === 1,
    'product now in cart after move-to-cart'
);

// Add another wishlist item to delete
$wl2Pid = (int) $pdo->query("SELECT id FROM products WHERE is_active=1 AND id NOT IN ($productId,$wlPid) AND stock>=10 ORDER BY id LIMIT 1")->fetchColumn();
$wl2 = api('POST', '/wishlist', $access, ['product_id' => $wl2Pid]);
$wl2Id = (int) ($wl2['json']['data']['id'] ?? $pdo->query("SELECT id FROM wishlists WHERE customer_id=$customerId AND product_id=$wl2Pid")->fetchColumn());
$wlDel = api('DELETE', '/wishlist/' . $wl2Id, $access);
assert_true(($wlDel['json']['success'] ?? false) === true, 'DELETE /wishlist/{id}');
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM wishlists WHERE id=$wl2Id")->fetchColumn() === 0,
    'wishlist delete removed DB row'
);

// ---------------------------------------------------------------------------
// Orders: place, invoice, cancel(+stock), reorder
// ---------------------------------------------------------------------------
// Ensure cart has primary product with enough stock tracked
$stockBeforePlace = (float) $pdo->query("SELECT stock FROM products WHERE id=$productId")->fetchColumn();
$place = api('POST', '/orders', $access, ['address_id' => $addrId]);
assert_true(($place['json']['success'] ?? false) === true || $place['code'] === 201, 'POST /orders place');
$orderId = (int) ($place['json']['data']['order']['id'] ?? 0);
$orderNumber = (string) ($place['json']['data']['order']['order_number'] ?? '');
assert_true($orderId > 0, "placed order id=$orderId");
assert_true(
    $pdo->query("SELECT status FROM orders WHERE id=$orderId")->fetchColumn() === 'placed',
    'order status placed in DB'
);

$inv = api('GET', '/orders/' . $orderId . '/invoice', $access);
assert_true(($inv['json']['success'] ?? false) === true, 'GET /orders/{id}/invoice');
assert_true(
    str_contains((string) ($inv['json']['data']['invoice']['invoice_number'] ?? ''), 'INV-')
    || str_contains($inv['raw'], 'INV-'),
    'invoice_number present'
);

// Confirm via OrderService to deduct stock, then cancel via API (must restore)
$adminId = (int) $pdo->query("SELECT id FROM admin_users WHERE role_type='super_admin' LIMIT 1")->fetchColumn();
$stockBeforeConfirm = (float) $pdo->query("SELECT stock FROM products WHERE id=$productId")->fetchColumn();
$orderQty = (float) $pdo->query("SELECT quantity FROM order_items WHERE order_id=$orderId AND product_id=$productId LIMIT 1")->fetchColumn();
$svc = new OrderService($pdo);
$svc->changeStatus($orderId, 'confirmed', $adminId);
$stockAfterConfirm = (float) $pdo->query("SELECT stock FROM products WHERE id=$productId")->fetchColumn();
assert_true(abs(($stockBeforeConfirm - $orderQty) - $stockAfterConfirm) < 0.001, 'stock deducted on confirm (pre-cancel)');

$cancel = api('POST', '/orders/' . $orderId . '/cancel', $access, ['reason' => 'verify_api cancel']);
assert_true(($cancel['json']['success'] ?? false) === true, 'POST /orders/{id}/cancel');
assert_true(
    $pdo->query("SELECT status FROM orders WHERE id=$orderId")->fetchColumn() === 'cancelled',
    'cancel set status=cancelled in DB'
);
$stockAfterCancel = (float) $pdo->query("SELECT stock FROM products WHERE id=$productId")->fetchColumn();
assert_true(abs($stockAfterCancel - $stockBeforeConfirm) < 0.001, 'cancel restored stock in DB');

// Place another order to reorder from (leave as placed/cancelled source)
$pdo->prepare('DELETE FROM cart_items WHERE customer_id = ?')->execute([$customerId]);
api('POST', '/cart/items', $access, ['product_id' => $productId, 'quantity' => $qty, 'replace' => true]);
$place2 = api('POST', '/orders', $access, ['address_id' => $addrId]);
$order2Id = (int) ($place2['json']['data']['order']['id'] ?? 0);
assert_true($order2Id > 0, 'second order for reorder source');

$pdo->prepare('DELETE FROM cart_items WHERE customer_id = ?')->execute([$customerId]);
$reorder = api('POST', '/orders/' . $order2Id . '/reorder', $access);
assert_true(($reorder['json']['success'] ?? false) === true, 'POST /orders/{id}/reorder');
assert_true(!empty($reorder['json']['data']['added']), 'reorder added items');
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM cart_items WHERE customer_id=$customerId")->fetchColumn() >= 1,
    'reorder populated cart in DB'
);

// ---------------------------------------------------------------------------
// Notifications: mark read + read-all
// ---------------------------------------------------------------------------
NotificationService::notifyCustomer($customerId, 'Verify ping', 'verify_api notification', 'offer', null);
$nId = (int) $pdo->query("SELECT id FROM notifications WHERE customer_id=$customerId ORDER BY id DESC LIMIT 1")->fetchColumn();
assert_true($nId > 0, 'notification seeded');
assert_true((int) $pdo->query("SELECT is_read FROM notifications WHERE id=$nId")->fetchColumn() === 0, 'notification unread');

$nRead = api('POST', '/notifications/' . $nId . '/read', $access);
assert_true(($nRead['json']['success'] ?? false) === true, 'POST /notifications/{id}/read');
assert_true((int) $pdo->query("SELECT is_read FROM notifications WHERE id=$nId")->fetchColumn() === 1, 'notification marked read in DB');

NotificationService::notifyCustomer($customerId, 'Verify ping 2', 'another', 'offer', null);
$nAll = api('POST', '/notifications/read-all', $access);
assert_true(($nAll['json']['success'] ?? false) === true, 'POST /notifications/read-all');
$unread = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE customer_id=$customerId AND is_read=0")->fetchColumn();
assert_true($unread === 0, 'all notifications read in DB');

// ---------------------------------------------------------------------------
// Support tickets: create + detail
// ---------------------------------------------------------------------------
$ticket = api('POST', '/support/tickets', $access, [
    'subject_type' => 'Order issue',
    'description'  => 'verify_api ticket body',
    'related_order_id' => $order2Id,
]);
assert_true(($ticket['json']['success'] ?? false) === true || $ticket['code'] === 201, 'POST /support/tickets');
$ticketId = (int) ($ticket['json']['data']['ticket']['id'] ?? 0);
assert_true($ticketId > 0, "ticket id=$ticketId");
assert_true(
    (int) $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE id=$ticketId AND customer_id=$customerId")->fetchColumn() === 1,
    'ticket row in DB'
);

$td = api('GET', '/support/tickets/' . $ticketId, $access);
assert_true(($td['json']['success'] ?? false) === true, 'GET /support/tickets/{id}');
assert_true((int) ($td['json']['data']['ticket']['id'] ?? 0) === $ticketId, 'ticket detail id matches');

// Cleanup temp files
@unlink($png);
@unlink($png2);

echo "\n";
if ($fail === 0) {
    echo "All API endpoint checks passed (core + previously untested).\n";
    echo "Base: $apiBase\n";
    exit(0);
}

echo "$fail API check(s) failed.\n";
exit(1);
