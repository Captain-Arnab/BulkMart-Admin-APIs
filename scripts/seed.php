<?php
/**
 * Seed categories, 34 products, and Super Admin.
 * Usage: php scripts/seed.php
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

$seedProducts = [
    // Green Vegetables
    ['Tomato', 'Green Vegetables', 'per kg', 10, 28, 250, 'VG-GV-001', 'A', 'Maharashtra'],
    ['Cucumber', 'Green Vegetables', 'per kg', 5, 32, 180, 'VG-GV-002', 'A', 'Karnataka'],
    ['Capsicum Green', 'Green Vegetables', 'per kg', 5, 55, 120, 'VG-GV-003', 'Premium', 'Maharashtra'],
    ['Bottle Gourd', 'Green Vegetables', 'per kg', 5, 30, 140, 'VG-GV-004', 'A', 'Gujarat'],
    ['Bitter Gourd', 'Green Vegetables', 'per kg', 5, 45, 90, 'VG-GV-005', 'A', 'Maharashtra'],
    ['Ridge Gourd', 'Green Vegetables', 'per kg', 5, 40, 85, 'VG-GV-006', 'B', 'Andhra Pradesh'],
    ['Lady Finger', 'Green Vegetables', 'per kg', 5, 42, 160, 'VG-GV-007', 'A', 'Maharashtra'],
    ['French Beans', 'Green Vegetables', 'per kg', 5, 60, 70, 'VG-GV-008', 'Premium', 'Karnataka'],
    ['Cabbage', 'Green Vegetables', 'per kg', 10, 22, 300, 'VG-GV-009', 'A', 'Himachal'],
    ['Cauliflower', 'Green Vegetables', 'per kg', 10, 35, 200, 'VG-GV-010', 'A', 'Punjab'],

    // Root Vegetables
    ['Potato', 'Root Vegetables', 'per kg', 25, 18, 800, 'VG-RV-001', 'A', 'Uttar Pradesh'],
    ['Onion', 'Root Vegetables', 'per kg', 25, 24, 750, 'VG-RV-002', 'A', 'Maharashtra'],
    ['Carrot', 'Root Vegetables', 'per kg', 10, 36, 220, 'VG-RV-003', 'Premium', 'Himachal'],
    ['Beetroot', 'Root Vegetables', 'per kg', 5, 38, 110, 'VG-RV-004', 'A', 'Karnataka'],
    ['Radish', 'Root Vegetables', 'per kg', 5, 20, 150, 'VG-RV-005', 'B', 'Haryana'],
    ['Sweet Potato', 'Root Vegetables', 'per kg', 10, 34, 130, 'VG-RV-006', 'A', 'Odisha'],
    ['Ginger', 'Root Vegetables', 'per kg', 2, 120, 60, 'VG-RV-007', 'Premium', 'Kerala'],
    ['Garlic', 'Root Vegetables', 'per kg', 2, 140, 55, 'VG-RV-008', 'A', 'Madhya Pradesh'],

    // Seasonal Fruits
    ['Banana', 'Seasonal Fruits', 'per dozen', 5, 45, 400, 'VG-SF-001', 'A', 'Tamil Nadu'],
    ['Apple', 'Seasonal Fruits', 'per kg', 5, 160, 180, 'VG-SF-002', 'Premium', 'Himachal'],
    ['Orange', 'Seasonal Fruits', 'per kg', 5, 70, 210, 'VG-SF-003', 'A', 'Nagpur'],
    ['Papaya', 'Seasonal Fruits', 'per kg', 5, 32, 160, 'VG-SF-004', 'A', 'Andhra Pradesh'],
    ['Watermelon', 'Seasonal Fruits', 'per kg', 10, 18, 500, 'VG-SF-005', 'B', 'Rajasthan'],
    ['Mango', 'Seasonal Fruits', 'per kg', 5, 95, 140, 'VG-SF-006', 'Premium', 'Ratnagiri'],
    ['Pomegranate', 'Seasonal Fruits', 'per kg', 5, 130, 95, 'VG-SF-007', 'Premium', 'Maharashtra'],
    ['Guava', 'Seasonal Fruits', 'per kg', 5, 48, 120, 'VG-SF-008', 'A', 'Uttar Pradesh'],
    ['Grapes', 'Seasonal Fruits', 'per kg', 5, 85, 100, 'VG-SF-009', 'A', 'Nashik'],
    ['Mosambi', 'Seasonal Fruits', 'per kg', 5, 55, 170, 'VG-SF-010', 'A', 'Andhra Pradesh'],

    // Herbs & Leafy
    ['Coriander', 'Herbs & Leafy', 'per bunch', 10, 12, 200, 'VG-HL-001', 'A', 'Maharashtra'],
    ['Mint', 'Herbs & Leafy', 'per bunch', 10, 15, 160, 'VG-HL-002', 'A', 'Maharashtra'],
    ['Spinach', 'Herbs & Leafy', 'per kg', 5, 28, 140, 'VG-HL-003', 'A', 'Gujarat'],
    ['Methi', 'Herbs & Leafy', 'per bunch', 10, 14, 150, 'VG-HL-004', 'B', 'Rajasthan'],
    ['Curry Leaves', 'Herbs & Leafy', 'per bunch', 10, 10, 180, 'VG-HL-005', 'A', 'Tamil Nadu'],
    ['Lettuce', 'Herbs & Leafy', 'per kg', 2, 75, 40, 'VG-HL-006', 'Premium', 'Maharashtra'],
];

$added = 0;
$skipped = 0;
foreach ($seedProducts as $row) {
    [$name, $cat, $unit, $moq, $price, $stock, $code, $grade, $origin] = $row;
    if ($products->findByItemCode($code)) {
        echo "[skip product] $code $name\n";
        $skipped++;
        continue;
    }
    $products->create([
        'category_id' => $categoryIds[$cat],
        'name'        => $name,
        'unit'        => $unit,
        'moq'         => $moq,
        'price'       => $price,
        'stock'       => $stock,
        'image_url'   => null,
        'batch_no'    => 'SEED-BATCH-01',
        'item_code'   => $code,
        'description' => "Wholesale {$name} — B2B supply.",
        'grade'       => $grade,
        'origin'      => $origin,
        'in_stock'    => $stock > 0,
        'is_active'   => true,
    ]);
    echo "[add product]  $code $name\n";
    $added++;
}

echo "\nDone. Categories: " . count($categoryIds) . ", products added: $added, skipped: $skipped\n";
echo "Login: " . SEED_ADMIN_EMAIL . " / " . SEED_ADMIN_PASSWORD . "\n";
