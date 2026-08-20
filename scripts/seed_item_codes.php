<?php
/**
 * Assign random unique item_code values to products.
 *
 * Usage:
 *   php scripts/seed_item_codes.php
 *       → fills only products where item_code is NULL/empty
 *
 *   php scripts/seed_item_codes.php --force
 *       → regenerates item_code for ALL products
 *
 * Format: VC-XXXXXX (e.g. VC-A7K2M9)
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';
require_once dirname(__DIR__) . '/app/models/Product.php';

$force = in_array('--force', $argv ?? [], true);

$pdo = db();
$products = new Product($pdo);

if ($force) {
    $rows = $pdo->query('SELECT id, name, item_code FROM products ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    echo "Mode: FORCE — regenerating item_code for all products.\n";
} else {
    $rows = $pdo->query(
        "SELECT id, name, item_code
         FROM products
         WHERE item_code IS NULL OR TRIM(item_code) = ''
         ORDER BY id ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
    echo "Mode: fill missing only (use --force to regenerate all).\n";
}

if (!$rows) {
    echo "Nothing to update — every product already has an item_code.\n";
    exit(0);
}

$updated = 0;
$stmt = $pdo->prepare('UPDATE products SET item_code = ? WHERE id = ?');

foreach ($rows as $row) {
    $id = (int) $row['id'];
    $old = trim((string) ($row['item_code'] ?? ''));
    $code = $products->generateUniqueItemCode();
    $stmt->execute([$code, $id]);
    $updated++;
    $label = $old !== '' ? "{$old} → {$code}" : $code;
    echo "[ok] #{$id} {$row['name']}: {$label}\n";
}

echo "\nDone. Updated {$updated} product(s).\n";
