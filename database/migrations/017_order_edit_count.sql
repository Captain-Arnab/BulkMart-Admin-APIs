-- 017_order_edit_count.sql
-- Customers may modify an order once (PUT /api/v1/orders/{id}); edit_count >= 1 blocks further edits.

SET NAMES utf8mb4;

ALTER TABLE `orders`
  ADD COLUMN `edit_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `status`;
