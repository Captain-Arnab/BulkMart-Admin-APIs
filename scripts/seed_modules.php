<?php
/**
 * Seed demo data for remaining modules + TEST Sub-Admin.
 * Usage: php scripts/seed_modules.php
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';
require_once dirname(__DIR__) . '/app/models/AdminUser.php';
require_once dirname(__DIR__) . '/app/models/Banner.php';
require_once dirname(__DIR__) . '/app/models/Offer.php';
require_once dirname(__DIR__) . '/app/models/Category.php';
require_once dirname(__DIR__) . '/app/models/MarketPrice.php';

$pdo = db();
$admins = new AdminUser($pdo);

// TEST Sub-Admin: products + customers only
$subId = $admins->ensureUser('TEST Sub Admin', 'subadmin@veggiicart.com', 'SubAdmin@123', 'sub_admin', true);
$admins->syncModules($subId, ['products', 'customers']);
echo "TEST Sub-Admin id={$subId} (subadmin@veggiicart.com / SubAdmin@123) modules=products,customers\n";

$adminId = (int) ($admins->findByEmail(SEED_ADMIN_EMAIL)['id'] ?? 1);

// Demo banner (placeholder image path — use existing logo)
$bannerCount = (int) $pdo->query('SELECT COUNT(*) FROM banners')->fetchColumn();
if ($bannerCount === 0) {
    (new Banner($pdo))->create([
        'image_url'   => 'assets/img/logo-on-light.png',
        'title'       => 'DEMO — Fresh arrivals this week',
        'link'        => null,
        'active_from' => date('Y-m-d H:i:s'),
        'active_to'   => date('Y-m-d H:i:s', strtotime('+30 days')),
        'sort_order'  => 1,
        'is_active'   => 1,
    ]);
    (new Banner($pdo))->create([
        'image_url'   => 'assets/img/logo-on-light.png',
        'title'       => 'DEMO — Bulk order specials',
        'link'        => '/offers',
        'active_from' => date('Y-m-d H:i:s'),
        'active_to'   => date('Y-m-d H:i:s', strtotime('+14 days')),
        'sort_order'  => 2,
        'is_active'   => 1,
    ]);
    echo "[add] 2 demo banners\n";
} else {
    echo "[skip] banners already exist\n";
}

$offerCount = (int) $pdo->query('SELECT COUNT(*) FROM offers')->fetchColumn();
if ($offerCount === 0) {
    $cat = (new Category($pdo))->findByName('Green Vegetables');
    (new Offer($pdo))->create([
        'title' => 'DEMO — 10% off Green Vegetables',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'min_qty' => 20,
        'category_id' => $cat['id'] ?? null,
        'coupon_code' => 'GREEN10',
        'valid_from' => date('Y-m-d H:i:s'),
        'valid_till' => date('Y-m-d H:i:s', strtotime('+14 days')),
        'is_active' => 1,
    ]);
    (new Offer($pdo))->create([
        'title' => 'DEMO — Flat ₹50 off orders',
        'discount_type' => 'flat',
        'discount_value' => 50,
        'min_qty' => 1,
        'category_id' => null,
        'coupon_code' => 'FLAT50',
        'valid_from' => date('Y-m-d H:i:s'),
        'valid_till' => date('Y-m-d H:i:s', strtotime('+7 days')),
        'is_active' => 1,
    ]);
    echo "[add] 2 demo offers\n";
} else {
    echo "[skip] offers already exist\n";
}

// Sample document for first customer
$cid = (int) $pdo->query('SELECT id FROM customers ORDER BY id LIMIT 1')->fetchColumn();
if ($cid) {
    $docCount = (int) $pdo->query("SELECT COUNT(*) FROM customer_documents WHERE customer_id={$cid}")->fetchColumn();
    if ($docCount === 0) {
        $pdo->prepare(
            'INSERT INTO customer_documents (customer_id, document_type, file_url) VALUES (?,?,?)'
        )->execute([$cid, 'gst_certificate', 'assets/img/logo-on-light.png']);
        $pdo->prepare(
            'INSERT INTO customer_documents (customer_id, document_type, file_url) VALUES (?,?,?)'
        )->execute([$cid, 'pan_card', 'assets/img/logo-on-light.png']);
        echo "[add] demo documents for customer #{$cid}\n";
    }

    $ticketCount = (int) $pdo->query('SELECT COUNT(*) FROM support_tickets')->fetchColumn();
    if ($ticketCount === 0) {
        $orderId = $pdo->query('SELECT id FROM orders ORDER BY id LIMIT 1')->fetchColumn();
        $pdo->prepare(
            'INSERT INTO support_tickets (customer_id, subject_type, description, related_order_id, status)
             VALUES (?,?,?,?,?)'
        )->execute([
            $cid,
            'Order delay',
            'DEMO ticket — Customer asking about delivery timing for a recent order.',
            $orderId ?: null,
            'open',
        ]);
        $tid = (int) $pdo->lastInsertId();
        $pdo->prepare(
            'INSERT INTO support_tickets (customer_id, subject_type, description, related_order_id, status)
             VALUES (?,?,?,?,?)'
        )->execute([
            $cid,
            'Product quality',
            'DEMO ticket — Query about vegetable freshness on last delivery.',
            null,
            'in_progress',
        ]);
        $tid2 = (int) $pdo->lastInsertId();
        $pdo->prepare(
            'INSERT INTO support_ticket_replies (ticket_id, admin_user_id, message) VALUES (?,?,?)'
        )->execute([$tid2, $adminId, 'DEMO reply — We are checking with the delivery team and will update you shortly.']);
        echo "[add] 2 demo support tickets\n";
    }

    // Mark one customer pending for KYC demo if all approved
    $pdo->exec("UPDATE customers SET kyc_status='pending' WHERE id={$cid} AND kyc_status='approved' LIMIT 1");
}

// Seed a few today's market prices
$mp = new MarketPrice($pdo);
$products = $pdo->query('SELECT id, price FROM products WHERE is_active=1 ORDER BY id LIMIT 5')->fetchAll();
foreach ($products as $p) {
    $mp->upsertToday((int) $p['id'], round((float) $p['price'] * 1.02, 2), $adminId);
}
echo "[add/update] market prices for " . count($products) . " products today\n";

echo "Done.\n";
