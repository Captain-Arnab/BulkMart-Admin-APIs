-- seed_item_codes.sql
-- Fill random-looking unique item_code for products that do not have one yet.
-- Format: VC-XXXXXX (derived from product id — unique, no PHP needed)
--
-- Usage (phpMyAdmin / MySQL client / HeidiSQL):
--   Run this file against your veggiicart database.
--
-- Safe to re-run: only updates rows where item_code is NULL or empty.
-- Existing codes (e.g. Tomato = 20) are left unchanged.

SET NAMES utf8mb4;

START TRANSACTION;

UPDATE `products`
SET `item_code` = CONCAT(
    'VC-',
    UPPER(SUBSTRING(MD5(CONCAT('veggiicart-item-', `id`)), 1, 6))
)
WHERE `item_code` IS NULL
   OR TRIM(`item_code`) = '';

COMMIT;

-- ---------------------------------------------------------------------------
-- OPTIONAL: regenerate item_code for ALL products (overwrites existing codes)
-- Uncomment the block below only if you really want every SKU replaced.
-- ---------------------------------------------------------------------------
-- START TRANSACTION;
-- UPDATE `products`
-- SET `item_code` = CONCAT(
--     'VC-',
--     UPPER(SUBSTRING(MD5(CONCAT('veggiicart-item-force-', `id`, '-', UNIX_TIMESTAMP())), 1, 6))
-- );
-- COMMIT;
