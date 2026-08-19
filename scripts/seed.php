<?php
/**
 * Seed categories, confirmed product catalog, and Super Admin.
 * Usage: php scripts/seed.php
 *
 * Idempotent: upserts the confirmed list by name, then removes any product
 * not on the list (VG-TEST-001, old placeholders, leftover seed SKUs).
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/config/db.php';
require_once dirname(__DIR__) . '/app/core/Model.php';
require_once dirname(__DIR__) . '/app/models/Category.php';
require_once dirname(__DIR__) . '/app/models/Product.php';
require_once dirname(__DIR__) . '/app/models/AdminUser.php';

$pdo = db();
$categories = new Category($pdo);
$products = new Product($pdo);
$admins = new AdminUser($pdo);

$adminId = $admins->ensureSuperAdmin(SEED_ADMIN_NAME, SEED_ADMIN_EMAIL, SEED_ADMIN_PASSWORD);
echo 'Super Admin id=' . $adminId . ' (' . SEED_ADMIN_EMAIL . ')' . PHP_EOL;

$categoryNames = [
    'Green Vegetables',
    'Root Vegetables',
    'Seasonal Fruits',
    'Herbs & Leafy',
];

$categoryIds = [];
foreach ($categoryNames as $name) {
    $row = $categories->findByName($name);
    if ($row) {
        $categoryIds[$name] = (int) $row['id'];
        echo "[skip category] $name\n";
    } else {
        $categoryIds[$name] = $categories->create(['name' => $name, 'image_url' => null]);
        echo "[add category]  $name\n";
    }
}

/**
 * Confirmed catalog. Names are stored exactly as given (including casing).
 * item_code / batch_no are NULL except Tomato item_code = '20'.
 *
 * Tuple: name, category, unit, moq, price, stock, item_code|null
 */
$seedProducts = [
    // Green Vegetables
    ['Beans', 'Green Vegetables', 'per kg', 5, 60, 70, null],
    ['Beans (chikkudu)', 'Green Vegetables', 'per kg', 5, 55, 80, null],
    ['Bittergourd', 'Green Vegetables', 'per kg', 5, 45, 90, null],
    ['Bottle gourd', 'Green Vegetables', 'per kg', 5, 30, 140, null],
    ['Brinjal (black)', 'Green Vegetables', 'per kg', 5, 36, 120, null],
    ['Brinjal (white)', 'Green Vegetables', 'per kg', 5, 38, 90, null],
    ['Brinjal long (purple)', 'Green Vegetables', 'per kg', 5, 40, 100, null],
    ['Cabbage (big size)', 'Green Vegetables', 'per kg', 10, 22, 300, null],
    ['cabbage (small size)', 'Green Vegetables', 'per kg', 10, 20, 220, null],
    ['Cluster beans', 'Green Vegetables', 'per kg', 5, 48, 85, null],
    ['Cucumber (English) black', 'Green Vegetables', 'per kg', 5, 32, 180, null],
    ['Cucumber yellow (round)', 'Green Vegetables', 'per kg', 5, 28, 150, null],
    ['Drumsticks', 'Green Vegetables', 'per kg', 5, 70, 60, null],
    ['Ivy gourd', 'Green Vegetables', 'per kg', 5, 42, 95, null],
    ['Ladyfinger', 'Green Vegetables', 'per kg', 5, 42, 160, null],
    ['Tomato', 'Green Vegetables', 'per kg', 10, 28, 250, '20'],
    ['capcicum green', 'Green Vegetables', 'per kg', 5, 55, 120, null],
    ['capsicum red', 'Green Vegetables', 'per kg', 5, 70, 80, null],

    // Root Vegetables
    ['Arvi (chamagadda)', 'Root Vegetables', 'per kg', 5, 40, 90, null],
    ['Beetroot', 'Root Vegetables', 'per kg', 5, 38, 110, null],
    ['Carrot', 'Root Vegetables', 'per kg', 10, 36, 220, null],
    ['Garlic', 'Root Vegetables', 'per kg', 2, 140, 55, null],
    ['Ginger', 'Root Vegetables', 'per kg', 2, 120, 60, null],
    ['Onion big size', 'Root Vegetables', 'per kg', 25, 24, 400, null],
    ['Onion medium size', 'Root Vegetables', 'per kg', 25, 22, 450, null],
    ['Onion small size', 'Root Vegetables', 'per kg', 25, 26, 300, null],
    ['Potato', 'Root Vegetables', 'per kg', 25, 18, 800, null],

    // Seasonal Fruits
    ['Lemon', 'Seasonal Fruits', 'per kg', 5, 50, 140, null],
    ['apple', 'Seasonal Fruits', 'per kg', 5, 160, 180, null],
    ['avocado', 'Seasonal Fruits', 'per kg', 2, 180, 40, null],
    ['banana', 'Seasonal Fruits', 'per dozen', 5, 45, 400, null],
    ['boppaya (papaya)', 'Seasonal Fruits', 'per kg', 5, 32, 160, null],

    // Herbs & Leafy
    ['Coriander leaves', 'Herbs & Leafy', 'per bunch', 10, 12, 200, null],
    ['Mint leaves', 'Herbs & Leafy', 'per bunch', 10, 15, 160, null],
];

$keepNames = [];
foreach ($seedProducts as $row) {
    $keepNames[] = $row[0];
}

// Drop old placeholder codes so Tomato can take item_code = 20 without unique clashes.
$pdo->exec('UPDATE products SET item_code = NULL, batch_no = NULL');

$added = 0;
$updated = 0;
foreach ($seedProducts as $row) {
    [$name, $cat, $unit, $moq, $price, $stock, $code] = $row;
    $existing = $products->findByName($name);
    $payload = [
        'category_id' => $categoryIds[$cat],
        'name'        => $name,
        'unit'        => $unit,
        'moq'         => $moq,
        'price'       => $price,
        'stock'       => $stock,
        'image_url'   => $existing['image_url'] ?? null,
        'batch_no'    => null,
        'item_code'   => $code,
        'description' => $existing['description'] ?? null,
        'grade'       => $existing['grade'] ?? null,
        'origin'      => $existing['origin'] ?? null,
        'in_stock'    => $stock > 0,
        'is_active'   => true,
    ];
    if ($existing) {
        // Keep live commercial fields if already set; always sync identity fields.
        $payload['unit'] = $existing['unit'] ?: $unit;
        $payload['moq'] = $existing['moq'] !== null && $existing['moq'] !== '' ? $existing['moq'] : $moq;
        $payload['price'] = $existing['price'] !== null && $existing['price'] !== '' ? $existing['price'] : $price;
        $payload['stock'] = $existing['stock'] !== null && $existing['stock'] !== '' ? $existing['stock'] : $stock;
        $payload['in_stock'] = ((float) $payload['stock']) > 0;
        $products->update((int) $existing['id'], $payload);
        echo "[update product] $name" . ($code ? " (item_code=$code)" : '') . "\n";
        $updated++;
    } else {
        $products->create($payload);
        echo "[add product]    $name" . ($code ? " (item_code=$code)" : '') . "\n";
        $added++;
    }
}

$placeholders = implode(',', array_fill(0, count($keepNames), '?'));
$extraStmt = $pdo->prepare("SELECT id, name, item_code FROM products WHERE name NOT IN ($placeholders)");
$extraStmt->execute($keepNames);
$extras = $extraStmt->fetchAll();
$removed = 0;
foreach ($extras as $extra) {
    $id = (int) $extra['id'];
    $pdo->prepare('DELETE FROM order_items WHERE product_id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    echo '[remove product] ' . $extra['name'] . ($extra['item_code'] ? ' (' . $extra['item_code'] . ')' : '') . "\n";
    $removed++;
}

$total = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
echo "\nDone. Categories: " . count($categoryIds)
    . ", added: $added, updated: $updated, removed: $removed, catalog total: $total\n";
echo 'Expected catalog size: ' . count($seedProducts) . "\n";
echo 'Login: ' . SEED_ADMIN_EMAIL . ' / ' . SEED_ADMIN_PASSWORD . "\n";

if ($total !== count($seedProducts)) {
    fwrite(STDERR, "WARNING: product count $total does not match seed list " . count($seedProducts) . "\n");
    exit(1);
}
