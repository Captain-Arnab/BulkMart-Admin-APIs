<?php
/**
 * Seed TEST Delivery Manager + sample customers/orders.
 * Usage: php scripts/seed_orders.php
 *
 * TEST Delivery Manager (remove before go-live):
 *   delivery@veggiicart.com / Delivery@123
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';
require_once dirname(__DIR__) . '/app/models/AdminUser.php';
require_once dirname(__DIR__) . '/app/models/Order.php';

$pdo = db();
$admins = new AdminUser($pdo);

$dmId = $admins->ensureUser(
    'TEST Delivery Manager',
    'delivery@veggiicart.com',
    'Delivery@123',
    'delivery_manager',
    true
);
$admins->grantModule($dmId, 'delivery');
$admins->grantModule($dmId, 'profile');
echo "TEST Delivery Manager id={$dmId} (delivery@veggiicart.com / Delivery@123)\n";

$super = $admins->findByEmail(SEED_ADMIN_EMAIL);
$superId = $super ? (int) $super['id'] : 1;

// Customers
$customers = [
    [
        'mobile' => '9876500001',
        'email' => 'freshmart@example.com',
        'business_name' => 'Fresh Mart Wholesale',
        'owner_name' => 'Ravi Kumar',
        'business_type' => 'Retailer',
        'gst_number' => '27AABCF1234A1Z5',
        'address' => [
            'label' => 'Shop',
            'line1' => '12 Market Road',
            'line2' => 'Near City Circle',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'pincode' => '411001',
            'landmark' => 'Opposite bus stand',
        ],
    ],
    [
        'mobile' => '9876500002',
        'email' => 'greengrocery@example.com',
        'business_name' => 'Green Grocery Co',
        'owner_name' => 'Sneha Patil',
        'business_type' => 'Hotel',
        'gst_number' => '27AABCG5678B1Z9',
        'address' => [
            'label' => 'Kitchen',
            'line1' => '88 Hospitality Lane',
            'line2' => null,
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'landmark' => null,
        ],
    ],
    [
        'mobile' => '9876500003',
        'email' => null,
        'business_name' => 'Daily Veg Hub',
        'owner_name' => 'Amit Shah',
        'business_type' => 'Kirana',
        'gst_number' => null,
        'address' => [
            'label' => 'Store',
            'line1' => '5 Station Chowk',
            'line2' => 'Shop no. 14',
            'city' => 'Nashik',
            'state' => 'Maharashtra',
            'pincode' => '422001',
            'landmark' => 'Near railway station',
        ],
    ],
];

$customerIds = [];
$addressIds = [];
foreach ($customers as $c) {
    $stmt = $pdo->prepare('SELECT id FROM customers WHERE mobile = ?');
    $stmt->execute([$c['mobile']]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        $cid = (int) $existing;
        echo "[skip customer] {$c['business_name']}\n";
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO customers (mobile, email, business_name, owner_name, business_type, gst_number, kyc_status)
             VALUES (?,?,?,?,?,?,\'approved\')'
        );
        $stmt->execute([
            $c['mobile'], $c['email'], $c['business_name'], $c['owner_name'], $c['business_type'], $c['gst_number'],
        ]);
        $cid = (int) $pdo->lastInsertId();
        echo "[add customer] {$c['business_name']}\n";
    }
    $customerIds[] = $cid;

    $stmt = $pdo->prepare('SELECT id FROM addresses WHERE customer_id = ? AND is_default = 1 LIMIT 1');
    $stmt->execute([$cid]);
    $aid = $stmt->fetchColumn();
    if ($aid) {
        $addressIds[] = (int) $aid;
    } else {
        $a = $c['address'];
        $stmt = $pdo->prepare(
            'INSERT INTO addresses (customer_id, label, line1, line2, city, state, pincode, landmark, is_default)
             VALUES (?,?,?,?,?,?,?,?,1)'
        );
        $stmt->execute([$cid, $a['label'], $a['line1'], $a['line2'], $a['city'], $a['state'], $a['pincode'], $a['landmark']]);
        $addressIds[] = (int) $pdo->lastInsertId();
    }
}

// Pull products
$products = $pdo->query('SELECT id, name, unit, price, stock, item_code FROM products WHERE is_active = 1 ORDER BY id ASC LIMIT 12')->fetchAll();
if (count($products) < 4) {
    fwrite(STDERR, "Need seeded products first. Run php scripts/seed.php\n");
    exit(1);
}

function nextOrderNumber(PDO $pdo): string
{
    $n = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn() + 1;
    return 'VC-' . str_pad((string) $n, 5, '0', STR_PAD_LEFT);
}

function insertOrder(PDO $pdo, array $data, array $lines, ?int $logAdminId): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO orders
          (order_number, customer_id, address_id, status, subtotal, delivery_fee, total, payment_method,
           estimated_delivery_date, delivered_at, assigned_delivery_manager_id, placed_at)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $data['order_number'],
        $data['customer_id'],
        $data['address_id'],
        $data['status'],
        $data['subtotal'],
        $data['delivery_fee'],
        $data['total'],
        'COD',
        $data['estimated_delivery_date'] ?? null,
        $data['delivered_at'] ?? null,
        $data['assigned_delivery_manager_id'] ?? null,
        $data['placed_at'],
    ]);
    $oid = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name_snapshot, unit_snapshot, quantity, unit_price_snapshot, line_total)
         VALUES (?,?,?,?,?,?,?)'
    );
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

    $log = $pdo->prepare(
        'INSERT INTO order_status_log (order_id, status, changed_by_admin_id, note, changed_at) VALUES (?,?,?,?,?)'
    );
    $log->execute([$oid, 'placed', $logAdminId, 'Seeded order', $data['placed_at']]);
    if ($data['status'] !== 'placed') {
        $log->execute([$oid, $data['status'], $logAdminId, 'Seeded status', $data['placed_at']]);
    }

    return $oid;
}

function buildLines(array $products, array $picks): array
{
    $lines = [];
    $subtotal = 0.0;
    foreach ($picks as [$idx, $qty]) {
        $p = $products[$idx];
        $lineTotal = round((float) $p['price'] * $qty, 2);
        $subtotal += $lineTotal;
        $lines[] = [
            'product_id' => (int) $p['id'],
            'name' => $p['name'],
            'unit' => $p['unit'],
            'qty' => $qty,
            'price' => (float) $p['price'],
            'line_total' => $lineTotal,
        ];
    }
    return [$lines, $subtotal];
}

// Avoid re-seeding demo orders if already present
$existingDemo = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE order_number LIKE 'VC-%'")->fetchColumn();
if ($existingDemo >= 5) {
    echo "Demo orders already present ({$existingDemo}). Skipping order insert.\n";
    echo "Done.\n";
    exit(0);
}

$fee = 50.0;
$now = new DateTimeImmutable('now');

// 1) Placed — unassigned
[$lines, $sub] = buildLines($products, [[0, 10], [1, 5]]);
insertOrder($pdo, [
    'order_number' => nextOrderNumber($pdo),
    'customer_id' => $customerIds[0],
    'address_id' => $addressIds[0],
    'status' => 'placed',
    'subtotal' => $sub,
    'delivery_fee' => $fee,
    'total' => $sub + $fee,
    'placed_at' => $now->modify('-2 hours')->format('Y-m-d H:i:s'),
], $lines, $superId);
echo "[add order] placed\n";

// 2) Confirmed — assigned to DM (stock already conceptually deducted in real flow; seed does NOT deduct to keep catalog intact)
[$lines, $sub] = buildLines($products, [[2, 8], [10, 20]]);
insertOrder($pdo, [
    'order_number' => nextOrderNumber($pdo),
    'customer_id' => $customerIds[1],
    'address_id' => $addressIds[1],
    'status' => 'confirmed',
    'subtotal' => $sub,
    'delivery_fee' => $fee,
    'total' => $sub + $fee,
    'assigned_delivery_manager_id' => $dmId,
    'placed_at' => $now->modify('-1 day')->format('Y-m-d H:i:s'),
], $lines, $superId);
echo "[add order] confirmed (assigned)\n";

// 3) Delivery date set — assigned
[$lines, $sub] = buildLines($products, [[3, 12], [11, 15]]);
insertOrder($pdo, [
    'order_number' => nextOrderNumber($pdo),
    'customer_id' => $customerIds[2],
    'address_id' => $addressIds[2],
    'status' => 'delivery_date_set',
    'subtotal' => $sub,
    'delivery_fee' => $fee,
    'total' => $sub + $fee,
    'estimated_delivery_date' => $now->modify('+1 day')->format('Y-m-d'),
    'assigned_delivery_manager_id' => $dmId,
    'placed_at' => $now->modify('-2 days')->format('Y-m-d H:i:s'),
], $lines, $superId);
echo "[add order] delivery_date_set\n";

// 4) Out for delivery — assigned
[$lines, $sub] = buildLines($products, [[4, 6]]);
insertOrder($pdo, [
    'order_number' => nextOrderNumber($pdo),
    'customer_id' => $customerIds[0],
    'address_id' => $addressIds[0],
    'status' => 'out_for_delivery',
    'subtotal' => $sub,
    'delivery_fee' => $fee,
    'total' => $sub + $fee,
    'estimated_delivery_date' => $now->format('Y-m-d'),
    'assigned_delivery_manager_id' => $dmId,
    'placed_at' => $now->modify('-3 days')->format('Y-m-d H:i:s'),
], $lines, $superId);
echo "[add order] out_for_delivery\n";

// 5) Delivered — history
[$lines, $sub] = buildLines($products, [[5, 10], [6, 8]]);
insertOrder($pdo, [
    'order_number' => nextOrderNumber($pdo),
    'customer_id' => $customerIds[1],
    'address_id' => $addressIds[1],
    'status' => 'delivered',
    'subtotal' => $sub,
    'delivery_fee' => $fee,
    'total' => $sub + $fee,
    'estimated_delivery_date' => $now->modify('-1 day')->format('Y-m-d'),
    'delivered_at' => $now->modify('-20 hours')->format('Y-m-d H:i:s'),
    'assigned_delivery_manager_id' => $dmId,
    'placed_at' => $now->modify('-4 days')->format('Y-m-d H:i:s'),
], $lines, $superId);
echo "[add order] delivered\n";

// 6) Cancelled placed (no stock effect)
[$lines, $sub] = buildLines($products, [[7, 5]]);
insertOrder($pdo, [
    'order_number' => nextOrderNumber($pdo),
    'customer_id' => $customerIds[2],
    'address_id' => $addressIds[2],
    'status' => 'cancelled',
    'subtotal' => $sub,
    'delivery_fee' => $fee,
    'total' => $sub + $fee,
    'placed_at' => $now->modify('-5 days')->format('Y-m-d H:i:s'),
], $lines, $superId);
echo "[add order] cancelled\n";

echo "\nDone. Login as Super Admin or TEST DM:\n";
echo "  delivery@veggiicart.com / Delivery@123  (TEST — remove before go-live)\n";
