<?php
/**
 * End-to-end API smoke test (core flow).
 * Usage: php scripts/verify_api.php
 *
 * Hits the app via internal PHP bootstrap (no Apache required for CLI path),
 * and optionally via HTTP if VERIFY_API_BASE is set.
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

function assert_true(bool $cond, string $msg): void
{
    if (!$cond) {
        fwrite(STDERR, "FAIL: $msg\n");
        exit(1);
    }
    echo "OK: $msg\n";
}

$pdo = db();

// Ensure API tables exist
$tables = ['otp_codes', 'refresh_tokens', 'cart_items', 'delivery_slots'];
foreach ($tables as $t) {
    $exists = $pdo->query("SHOW TABLES LIKE '$t'")->fetchColumn();
    assert_true((bool) $exists, "table $t exists (run php scripts/migrate.php)");
}

$mobile = '9876599999'; // dedicated test mobile
$pdo->prepare('DELETE FROM otp_rate_limits WHERE mobile = ?')->execute([$mobile]);
$pdo->prepare('DELETE FROM otp_codes WHERE mobile = ?')->execute([$mobile]);

// Clean prior test customer cart/orders if re-run
$cust = (new Customer($pdo))->findByMobile($mobile);
if ($cust) {
    $cid = (int) $cust['id'];
    $pdo->prepare('DELETE FROM cart_items WHERE customer_id = ?')->execute([$cid]);
}

$otpSvc = new OtpService($pdo);
$sent = $otpSvc->sendLoginOtp($mobile);
assert_true(isset($sent['otp']) && strlen($sent['otp']) === 6, 'send-otp generated 6-digit OTP');
assert_true(!empty($sent['sms']['dev_mode']), 'SMS in DEV MODE (gateway not connected)');

$ok = $otpSvc->verifyLoginOtp($mobile, $sent['otp']);
assert_true($ok, 'verify-otp accepts correct OTP');

$customers = new Customer($pdo);
$customer = $customers->findByMobile($mobile);
if (!$customer) {
    $id = $customers->createFromMobile($mobile);
    $customer = $customers->find($id);
}
assert_true($customer !== null, 'customer exists after OTP');
$customerId = (int) $customer['id'];

// Ensure address
$addrModel = new Address($pdo);
$addrs = $addrModel->listForCustomer($customerId);
if ($addrs === []) {
    $addrId = $addrModel->create($customerId, [
        'label' => 'Test Shop',
        'line1' => '1 API Test Road',
        'city' => 'Pune',
        'state' => 'Maharashtra',
        'pincode' => '411001',
        'is_default' => true,
    ]);
} else {
    $addrId = (int) $addrs[0]['id'];
}
assert_true($addrId > 0, "address id=$addrId");

// JWT
$access = JwtService::issueAccessToken($customerId);
$payload = JwtService::decode($access);
assert_true((int) $payload['sub'] === $customerId, 'JWT access token decodes to customer');

$refresh = JwtService::issueRefreshToken();
$tokens = new RefreshToken($pdo);
$tokens->store($customerId, $refresh, 3600);
assert_true($tokens->findValid($refresh) !== null, 'refresh token stored');

// Products
$products = new Product($pdo);
$list = $products->paginateWithCategory(null, null, 1, 5, false, 20, true, false);
assert_true($list['total'] > 0, 'active products available (' . $list['total'] . ')');
$product = $list['rows'][0];
$productId = (int) $product['id'];
$moq = max(1.0, (float) $product['moq']);
$qty = max($moq, 1.0);
if ((float) $product['stock'] < $qty) {
    $products->updateStock($productId, $qty + 10);
    $product = $products->find($productId);
}
assert_true((float) $product['stock'] >= $qty, 'product has enough stock');

// Cart
$cart = new Cart($pdo);
$cart->clear($customerId);
$itemId = $cart->upsertItem($customerId, $productId, $qty);
assert_true($itemId > 0, "cart item id=$itemId");
$items = $cart->itemsForCustomer($customerId);
assert_true(count($items) === 1, 'cart has 1 item');

// Place order
$checkout = new CheckoutService($pdo);
$result = $checkout->placeCodOrder($customerId, ['address_id' => $addrId]);
$order = $result['order'];
assert_true(!empty($order['order_number']), 'order placed: ' . $order['order_number']);
assert_true($order['status'] === 'placed', 'order status is placed');
assert_true($order['payment_method'] === 'COD', 'payment method COD');
assert_true($cart->itemsForCustomer($customerId) === [], 'cart cleared after place');

$orderModel = new Order($pdo);
$detail = $orderModel->findForCustomer((int) $order['id'], $customerId);
assert_true($detail !== null, 'order detail readable for customer');
$tracking = $orderModel->statusLog((int) $order['id']);
assert_true($tracking !== [], 'order tracking log present');
assert_true(($tracking[0]['status'] ?? '') === 'placed', 'tracking starts at placed');

// Rate limit smoke (optional soft check)
echo "\nCore flow OK — send-otp → verify → products → cart → place order → detail\n";
echo "SMS gateway: DEV MODE (see storage/logs/sms_dev.log)\n";
echo "Order: {$order['order_number']} (id={$order['id']})\n";

// Optional HTTP check
$base = getenv('VERIFY_API_BASE') ?: '';
if ($base !== '') {
    echo "\nHTTP check against $base ...\n";
    $ch = curl_init(rtrim($base, '/') . '/categories');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    assert_true($code === 200 && str_contains((string) $body, '"success":true'), "HTTP GET /categories => $code");
}
