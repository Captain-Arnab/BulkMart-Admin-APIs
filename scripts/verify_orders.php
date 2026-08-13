<?php
/**
 * End-to-end verify: role gating, stock deduct/restore, delivery lifecycle.
 * Usage: php scripts/verify_orders.php
 */

declare(strict_types=1);

$base = getenv('VC_BASE') ?: 'http://localhost/VGS/veggiicart/public';

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';

spl_autoload_register(static function (string $class): void {
    foreach ([
        APP_PATH . '/services/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/core/' . $class . '.php',
    ] as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

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

$pdo = db();

// --- Role gating via HTTP ---
$cDm = tempnam(sys_get_temp_dir(), 'dm');
$r = req('POST', "$base/login", $cDm, ['identity' => 'delivery@veggiicart.com', 'password' => 'Delivery@123']);
ok('DM login redirects', in_array($r['code'], [302, 301], true) && str_contains((string)$r['location'], 'delivery'));

$r = req('GET', "$base/delivery", $cDm);
ok('DM can open delivery', $r['code'] === 200);
ok('DM sidebar has Delivery', str_contains($r['body'], 'Delivery Management'));
ok('DM sidebar hides Products', !str_contains($r['body'], 'Products & Stock'));
ok('DM sidebar hides Customers', !str_contains($r['body'], '>Customers<'));
ok('DM sidebar hides Reports', !str_contains($r['body'], 'Reports & Analytics'));

$r = req('GET', "$base/products", $cDm);
ok('DM blocked from products', in_array($r['code'], [302, 301], true));

$r = req('GET', "$base/orders", $cDm);
ok('DM blocked from orders module', in_array($r['code'], [302, 301], true));
@unlink($cDm);

$cAdmin = tempnam(sys_get_temp_dir(), 'sa');
$r = req('POST', "$base/login", $cAdmin, ['identity' => 'admin@veggiicart.com', 'password' => 'ChangeMe@123']);
ok('SA login', in_array($r['code'], [302, 301], true));
$r = req('GET', "$base/orders", $cAdmin);
ok('SA orders list', $r['code'] === 200 && str_contains($r['body'], 'VC-'));
$r = req('GET', "$base/delivery", $cAdmin);
ok('SA delivery view', $r['code'] === 200);

// --- Stock lifecycle via service (autoload resolves SmsService / NotificationService) ---
$dmId = (int) $pdo->query("SELECT id FROM admin_users WHERE email='delivery@veggiicart.com'")->fetchColumn();
$adminId = (int) $pdo->query("SELECT id FROM admin_users WHERE email='admin@veggiicart.com'")->fetchColumn();
$customerId = (int) $pdo->query('SELECT id FROM customers ORDER BY id LIMIT 1')->fetchColumn();
$addressId = (int) $pdo->query("SELECT id FROM addresses WHERE customer_id={$customerId} LIMIT 1")->fetchColumn();
$product = $pdo->query('SELECT id, name, unit, price, stock FROM products WHERE stock >= 50 ORDER BY id LIMIT 1')->fetch();
ok('Have product with stock for lifecycle', (bool) $product);

$before = (float) $product['stock'];
$qty = 5.0;
$sub = round($qty * (float)$product['price'], 2);
$fee = 40.0;
$orderNo = 'VC-TEST-' . time();

$pdo->prepare(
    'INSERT INTO orders (order_number, customer_id, address_id, status, subtotal, delivery_fee, total, payment_method, placed_at)
     VALUES (?,?,?,?,?,?,?,?,NOW())'
)->execute([$orderNo, $customerId, $addressId, 'placed', $sub, $fee, $sub + $fee, 'COD']);
$oid = (int) $pdo->lastInsertId();
$pdo->prepare(
    'INSERT INTO order_items (order_id, product_id, product_name_snapshot, unit_snapshot, quantity, unit_price_snapshot, line_total)
     VALUES (?,?,?,?,?,?,?)'
)->execute([$oid, $product['id'], $product['name'], $product['unit'], $qty, $product['price'], $sub]);
$pdo->prepare('INSERT INTO order_status_log (order_id, status, changed_by_admin_id, note) VALUES (?,?,?,?)')
    ->execute([$oid, 'placed', $adminId, 'verify script']);

$svc = new OrderService($pdo);
$svc->changeStatus($oid, 'confirmed', $adminId);
$afterConfirm = (float) $pdo->query('SELECT stock FROM products WHERE id=' . (int)$product['id'])->fetchColumn();
ok('Stock deducted on confirm', abs(($before - $qty) - $afterConfirm) < 0.001);

$svc->assignDeliveryManager($oid, $dmId, $adminId);
$assigned = (int) $pdo->query('SELECT assigned_delivery_manager_id FROM orders WHERE id=' . $oid)->fetchColumn();
ok('Assigned to DM', $assigned === $dmId);

$eta = date('Y-m-d', strtotime('+2 days'));
$svc->setDeliveryDate($oid, $eta, $dmId);
$status = $pdo->query('SELECT status FROM orders WHERE id=' . $oid)->fetchColumn();
ok('Status delivery_date_set', $status === 'delivery_date_set');

$svc->markOutForDelivery($oid, $dmId);
$status = $pdo->query('SELECT status FROM orders WHERE id=' . $oid)->fetchColumn();
ok('Status out_for_delivery', $status === 'out_for_delivery');

$res = $svc->markDelivered($oid, $dmId, $sub + $fee + 10, false);
ok('COD mismatch warns', !empty($res['needs_confirm']));
$res = $svc->markDelivered($oid, $dmId, $sub + $fee + 10, true);
ok('Delivered after COD ack', empty($res['needs_confirm']));
$status = $pdo->query('SELECT status, delivered_at FROM orders WHERE id=' . $oid)->fetch();
ok('Status delivered + timestamp', $status['status'] === 'delivered' && !empty($status['delivered_at']));

$logs = (int) $pdo->query("SELECT COUNT(*) FROM order_status_log WHERE order_id={$oid}")->fetchColumn();
ok('Status log has entries', $logs >= 4);

$notifs = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE related_id={$oid} AND type='order'")->fetchColumn();
ok('Customer notifications inserted', $notifs >= 3);

// Cancel + restore path
$product2 = $pdo->query('SELECT id, name, unit, price, stock FROM products WHERE id <> ' . (int)$product['id'] . ' AND stock >= 20 ORDER BY id LIMIT 1')->fetch();
$before2 = (float) $product2['stock'];
$qty2 = 3.0;
$sub2 = round($qty2 * (float)$product2['price'], 2);
$orderNo2 = 'VC-CXL-' . time();
$pdo->prepare(
    'INSERT INTO orders (order_number, customer_id, address_id, status, subtotal, delivery_fee, total, payment_method, placed_at)
     VALUES (?,?,?,?,?,?,?,?,NOW())'
)->execute([$orderNo2, $customerId, $addressId, 'placed', $sub2, 0, $sub2, 'COD']);
$oid2 = (int) $pdo->lastInsertId();
$pdo->prepare(
    'INSERT INTO order_items (order_id, product_id, product_name_snapshot, unit_snapshot, quantity, unit_price_snapshot, line_total)
     VALUES (?,?,?,?,?,?,?)'
)->execute([$oid2, $product2['id'], $product2['name'], $product2['unit'], $qty2, $product2['price'], $sub2]);

$svc->changeStatus($oid2, 'confirmed', $adminId);
$mid = (float) $pdo->query('SELECT stock FROM products WHERE id=' . (int)$product2['id'])->fetchColumn();
ok('Cancel-path stock deducted', abs(($before2 - $qty2) - $mid) < 0.001);
$svc->changeStatus($oid2, 'cancelled', $adminId);
$restored = (float) $pdo->query('SELECT stock FROM products WHERE id=' . (int)$product2['id'])->fetchColumn();
ok('Stock restored after cancel', abs($restored - $before2) < 0.001);

@unlink($cAdmin);

echo PHP_EOL . ($fail === 0 ? "All order/delivery checks passed.\n" : "$fail failed.\n");
exit($fail === 0 ? 0 : 1);
