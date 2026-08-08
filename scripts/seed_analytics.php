<?php
/**
 * DEMO analytics seed — 50+ orders across ~90 days for chart-friendly dashboards.
 * Safe to re-run: skips if VC-DEMO-* orders already exist in volume.
 *
 * Usage: php scripts/seed_analytics.php
 * (Requires seed.php + seed_orders.php first for products/customers/DM.)
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';

$pdo = db();

$existing = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE order_number LIKE 'VC-DEMO-%'")->fetchColumn();
if ($existing >= 40) {
    echo "DEMO analytics orders already present ({$existing}). Skipping.\n";
    exit(0);
}

$products = $pdo->query(
    'SELECT id, name, unit, price, category_id, stock FROM products WHERE is_active = 1 ORDER BY id'
)->fetchAll();
if (count($products) < 8) {
    fwrite(STDERR, "Need products first: php scripts/seed.php\n");
    exit(1);
}

// Extra DEMO customers for variety
$demoCustomers = [
    ['mobile' => '9876500101', 'business_name' => 'DEMO Sunrise Kirana', 'owner_name' => 'Demo User A', 'business_type' => 'Kirana', 'city' => 'Pune'],
    ['mobile' => '9876500102', 'business_name' => 'DEMO Hotel Leaf & Root', 'owner_name' => 'Demo User B', 'business_type' => 'Hotel', 'city' => 'Mumbai'],
    ['mobile' => '9876500103', 'business_name' => 'DEMO City Fresh Mart', 'owner_name' => 'Demo User C', 'business_type' => 'Retailer', 'city' => 'Nashik'],
    ['mobile' => '9876500104', 'business_name' => 'DEMO Green Basket Traders', 'owner_name' => 'Demo User D', 'business_type' => 'Wholesaler', 'city' => 'Nagpur'],
    ['mobile' => '9876500105', 'business_name' => 'DEMO Spice Route Cafe', 'owner_name' => 'Demo User E', 'business_type' => 'Hotel', 'city' => 'Pune'],
];

$customerPool = [];
foreach ($demoCustomers as $i => $c) {
    $stmt = $pdo->prepare('SELECT id FROM customers WHERE mobile = ?');
    $stmt->execute([$c['mobile']]);
    $cid = $stmt->fetchColumn();
    if (!$cid) {
        $pdo->prepare(
            "INSERT INTO customers (mobile, email, business_name, owner_name, business_type, kyc_status)
             VALUES (?,?,?,?,?,'approved')"
        )->execute([
            $c['mobile'],
            'demo' . ($i + 1) . '@veggiicart.test',
            $c['business_name'],
            $c['owner_name'],
            $c['business_type'],
        ]);
        $cid = (int) $pdo->lastInsertId();
        $pdo->prepare(
            'INSERT INTO addresses (customer_id, label, line1, city, state, pincode, is_default)
             VALUES (?,?,?,?,?,?,1)'
        )->execute([
            $cid,
            'Shop',
            'DEMO address line ' . ($i + 1),
            $c['city'],
            'Maharashtra',
            '41100' . ($i + 1),
        ]);
        echo "[add DEMO customer] {$c['business_name']}\n";
    } else {
        $cid = (int) $cid;
    }
    $stmt = $pdo->prepare('SELECT id FROM addresses WHERE customer_id = ? ORDER BY is_default DESC, id ASC LIMIT 1');
    $stmt->execute([$cid]);
    $aid = (int) $stmt->fetchColumn();
    $customerPool[] = ['id' => $cid, 'address_id' => $aid];
}

// Include existing non-demo customers too
$extra = $pdo->query(
    "SELECT c.id, (
        SELECT a.id FROM addresses a WHERE a.customer_id = c.id ORDER BY a.is_default DESC, a.id ASC LIMIT 1
     ) AS address_id
     FROM customers c
     WHERE c.mobile NOT LIKE '98765001%'
     LIMIT 5"
)->fetchAll();
foreach ($extra as $row) {
    if (empty($row['address_id'])) {
        continue;
    }
    $customerPool[] = ['id' => (int) $row['id'], 'address_id' => (int) $row['address_id']];
}

$dmId = (int) ($pdo->query(
    "SELECT id FROM admin_users WHERE role_type='delivery_manager' AND is_active=1 LIMIT 1"
)->fetchColumn() ?: 0);
$adminId = (int) ($pdo->query(
    "SELECT id FROM admin_users WHERE role_type='super_admin' LIMIT 1"
)->fetchColumn() ?: 1);

$statuses = [
    'delivered', 'delivered', 'delivered', 'delivered', 'delivered',
    'out_for_delivery', 'out_for_delivery',
    'delivery_date_set', 'delivery_date_set',
    'confirmed', 'confirmed',
    'placed', 'placed',
    'cancelled',
];

$fee = 50.0;
$seq = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE order_number LIKE 'VC-DEMO-%'")->fetchColumn();
$target = 55;
$created = 0;
$now = new DateTimeImmutable('now');

$orderStmt = $pdo->prepare(
    'INSERT INTO orders
      (order_number, customer_id, address_id, status, subtotal, delivery_fee, total, payment_method,
       estimated_delivery_date, delivered_at, assigned_delivery_manager_id, placed_at)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
);
$itemStmt = $pdo->prepare(
    'INSERT INTO order_items (order_id, product_id, product_name_snapshot, unit_snapshot, quantity, unit_price_snapshot, line_total)
     VALUES (?,?,?,?,?,?,?)'
);
$logStmt = $pdo->prepare(
    'INSERT INTO order_status_log (order_id, status, changed_by_admin_id, note, changed_at) VALUES (?,?,?,?,?)'
);

$pdo->beginTransaction();
try {
    for ($n = 0; $n < $target; $n++) {
        $seq++;
        $dayOffset = random_int(0, 89);
        $hour = random_int(6, 20);
        $minute = random_int(0, 59);
        $placed = $now->modify("-{$dayOffset} days")->setTime($hour, $minute, 0);
        $status = $statuses[array_rand($statuses)];
        $cust = $customerPool[array_rand($customerPool)];

        $pickCount = random_int(2, 5);
        $keys = array_rand($products, $pickCount);
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $lines = [];
        $subtotal = 0.0;
        foreach ($keys as $k) {
            $p = $products[$k];
            $qty = (float) random_int(2, 25);
            $line = round((float) $p['price'] * $qty, 2);
            $subtotal += $line;
            $lines[] = [
                'product_id' => (int) $p['id'],
                'name' => $p['name'],
                'unit' => $p['unit'],
                'qty' => $qty,
                'price' => (float) $p['price'],
                'line_total' => $line,
            ];
        }

        $eta = null;
        $deliveredAt = null;
        $dm = null;
        if (in_array($status, ['confirmed', 'delivery_date_set', 'out_for_delivery', 'delivered'], true) && $dmId) {
            $dm = $dmId;
        }
        if (in_array($status, ['delivery_date_set', 'out_for_delivery', 'delivered'], true)) {
            $eta = $placed->modify('+' . random_int(1, 3) . ' days')->format('Y-m-d');
        }
        if ($status === 'delivered') {
            $deliveredAt = $placed->modify('+' . random_int(1, 4) . ' days')->format('Y-m-d H:i:s');
        }

        $orderNo = 'VC-DEMO-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        $orderStmt->execute([
            $orderNo,
            $cust['id'],
            $cust['address_id'],
            $status,
            $subtotal,
            $fee,
            $subtotal + $fee,
            'COD',
            $eta,
            $deliveredAt,
            $dm,
            $placed->format('Y-m-d H:i:s'),
        ]);
        $oid = (int) $pdo->lastInsertId();
        foreach ($lines as $line) {
            $itemStmt->execute([
                $oid,
                $line['product_id'],
                $line['name'],
                $line['unit'],
                $line['qty'],
                $line['price'],
                $line['line_total'],
            ]);
        }
        $logStmt->execute([$oid, 'placed', $adminId, 'DEMO analytics seed', $placed->format('Y-m-d H:i:s')]);
        if ($status !== 'placed') {
            $logStmt->execute([$oid, $status, $adminId, 'DEMO analytics seed status', $placed->format('Y-m-d H:i:s')]);
        }
        $created++;
    }

    // Nudge a few products into low-stock for the watchlist
    $ids = $pdo->query('SELECT id FROM products WHERE is_active = 1 ORDER BY id ASC LIMIT 5')->fetchAll(PDO::FETCH_COLUMN);
    $lowLevels = [8, 12, 5, 15, 3];
    $upd = $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?');
    foreach ($ids as $i => $pid) {
        $upd->execute([$lowLevels[$i] ?? 10, (int) $pid]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Seed failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

echo "[add] {$created} DEMO analytics orders (VC-DEMO-*)\n";
echo "[nudge] 5 products set to low stock for watchlist\n";
echo "Done. Open Dashboard + Reports for populated charts.\n";
