-- products_production.sql
-- VeggiiCart confirmed catalog (34 products) for phpMyAdmin import.
-- Idempotent: safe to run more than once. Resolves category_id by name.
-- Generated from the verified local database. Do not hardcode category IDs.
-- Generated: 2026-08-19 08:09:26

SET NAMES utf8mb4;
SET time_zone = '+00:00';

START TRANSACTION;

-- ---------------------------------------------------------------------------
-- A) Categories (insert by name if missing; live auto-increment IDs may differ)
-- ---------------------------------------------------------------------------

INSERT INTO categories (name, image_url, created_at, updated_at)
SELECT 'Green Vegetables', 'uploads/categories/20260819075841_0c37df20.jpg', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Green Vegetables');

INSERT INTO categories (name, image_url, created_at, updated_at)
SELECT 'Root Vegetables', 'uploads/categories/20260819075725_fcdc3c2b.jpg', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Root Vegetables');

INSERT INTO categories (name, image_url, created_at, updated_at)
SELECT 'Seasonal Fruits', 'uploads/categories/20260819075731_4f38f853.jpg', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Seasonal Fruits');

INSERT INTO categories (name, image_url, created_at, updated_at)
SELECT 'Herbs & Leafy', 'uploads/categories/20260819075718_a5abba52.jpg', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Herbs & Leafy');

-- ---------------------------------------------------------------------------
-- B) Remove stale/test products by name/code (never the confirmed 34).
--     Skips any product still referenced by order_items (RESTRICT FK).
--     Demo/real order conflicts are not deleted here — flagged in PRODUCTION_IMPORT_NOTES.txt.
-- ---------------------------------------------------------------------------

DELETE FROM products
WHERE (
    name IN ('VG-TEST-001', 'Sample Capsicum', 'Sample Capsicum Green', 'Cauliflower', 'Fresh Cauliflower', 'Ridge Gourd', 'Ridge gourd', 'Basmati Rice', 'Rice', 'Toor Dal', 'Moong Dal', 'Sunflower Oil', 'Mustard Oil', 'Turmeric', 'Red Chilli', 'Wheat Flour', 'Atta', 'Sugar', 'Salt')
    OR item_code IN ('VG-TEST-001')
)
AND name NOT IN ('Beans', 'Beans (chikkudu)', 'Bittergourd', 'Bottle gourd', 'Brinjal (black)', 'Brinjal (white)', 'Brinjal long (purple)', 'Cabbage (big size)', 'cabbage (small size)', 'Cluster beans', 'Cucumber (English) black', 'Cucumber yellow (round)', 'Drumsticks', 'Ivy gourd', 'Ladyfinger', 'Tomato', 'capcicum green', 'capsicum red', 'Arvi (chamagadda)', 'Beetroot', 'Carrot', 'Garlic', 'Ginger', 'Onion big size', 'Onion medium size', 'Onion small size', 'Potato', 'Lemon', 'apple', 'avocado', 'banana', 'boppaya (papaya)', 'Coriander leaves', 'Mint leaves')
AND NOT EXISTS (
    SELECT 1 FROM order_items oi WHERE oi.product_id = products.id
);

-- ---------------------------------------------------------------------------
-- C) Confirmed 34 products (insert-if-missing by exact name)
-- ---------------------------------------------------------------------------

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Beans',
  'per kg',
  5,
  60,
  70,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Beans');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Beans (chikkudu)',
  'per kg',
  5,
  55,
  80,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Beans (chikkudu)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Bittergourd',
  'per kg',
  5,
  45,
  90,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Bittergourd');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Bottle gourd',
  'per kg',
  5,
  30,
  15,
  NULL,
  NULL,
  NULL,
  'Wholesale Bottle Gourd — B2B supply.',
  'A',
  'Gujarat',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Bottle gourd');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Brinjal (black)',
  'per kg',
  5,
  36,
  120,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Brinjal (black)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Brinjal (white)',
  'per kg',
  5,
  38,
  90,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Brinjal (white)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Brinjal long (purple)',
  'per kg',
  5,
  40,
  100,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Brinjal long (purple)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Cabbage (big size)',
  'per kg',
  10,
  22,
  300,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cabbage (big size)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'cabbage (small size)',
  'per kg',
  10,
  20,
  220,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'cabbage (small size)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Cluster beans',
  'per kg',
  5,
  48,
  85,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cluster beans');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Cucumber (English) black',
  'per kg',
  5,
  32,
  180,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cucumber (English) black');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Cucumber yellow (round)',
  'per kg',
  5,
  28,
  150,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cucumber yellow (round)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Drumsticks',
  'per kg',
  5,
  70,
  60,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Drumsticks');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Ivy gourd',
  'per kg',
  5,
  42,
  95,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Ivy gourd');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Ladyfinger',
  'per kg',
  5,
  42,
  160,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Ladyfinger');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'Tomato',
  'per kg',
  10,
  28,
  275,
  'uploads/products/test_1_a.png',
  NULL,
  '20',
  'Wholesale Tomato — B2B supply.',
  'A',
  'Maharashtra',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Tomato');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'capcicum green',
  'per kg',
  5,
  55,
  120,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'capcicum green');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Green Vegetables'),
  'capsicum red',
  'per kg',
  5,
  70,
  80,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'capsicum red');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Arvi (chamagadda)',
  'per kg',
  5,
  40,
  90,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Arvi (chamagadda)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Beetroot',
  'per kg',
  5,
  38,
  110,
  NULL,
  NULL,
  NULL,
  'Wholesale Beetroot — B2B supply.',
  'A',
  'Karnataka',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Beetroot');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Carrot',
  'per kg',
  10,
  36,
  220,
  NULL,
  NULL,
  NULL,
  'Wholesale Carrot — B2B supply.',
  'Premium',
  'Himachal',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Carrot');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Garlic',
  'per kg',
  2,
  140,
  55,
  NULL,
  NULL,
  NULL,
  'Wholesale Garlic — B2B supply.',
  'A',
  'Madhya Pradesh',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Garlic');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Ginger',
  'per kg',
  2,
  120,
  60,
  NULL,
  NULL,
  NULL,
  'Wholesale Ginger — B2B supply.',
  'Premium',
  'Kerala',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Ginger');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Onion big size',
  'per kg',
  25,
  24,
  400,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Onion big size');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Onion medium size',
  'per kg',
  25,
  22,
  450,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Onion medium size');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Onion small size',
  'per kg',
  25,
  26,
  300,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Onion small size');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Root Vegetables'),
  'Potato',
  'per kg',
  25,
  18,
  820,
  'uploads/products/test_11_a.png',
  NULL,
  NULL,
  'Wholesale Potato — B2B supply.',
  'A',
  'Uttar Pradesh',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Potato');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Seasonal Fruits'),
  'Lemon',
  'per kg',
  5,
  50,
  140,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Lemon');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Seasonal Fruits'),
  'apple',
  'per kg',
  5,
  160,
  180,
  NULL,
  NULL,
  NULL,
  'Wholesale Apple — B2B supply.',
  'Premium',
  'Himachal',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'apple');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Seasonal Fruits'),
  'avocado',
  'per kg',
  2,
  180,
  40,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'avocado');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Seasonal Fruits'),
  'banana',
  'per dozen',
  5,
  45,
  400,
  NULL,
  NULL,
  NULL,
  'Wholesale Banana — B2B supply.',
  'A',
  'Tamil Nadu',
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'banana');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Seasonal Fruits'),
  'boppaya (papaya)',
  'per kg',
  5,
  32,
  160,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'boppaya (papaya)');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Herbs & Leafy'),
  'Coriander leaves',
  'per bunch',
  10,
  12,
  200,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Coriander leaves');

INSERT INTO products (
  category_id, name, unit, moq, price, stock, image_url,
  batch_no, item_code, description, grade, origin, in_stock, is_active,
  created_at, updated_at
)
SELECT
  (SELECT id FROM categories WHERE name = 'Herbs & Leafy'),
  'Mint leaves',
  'per bunch',
  10,
  15,
  160,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  1,
  NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Mint leaves');

COMMIT;
