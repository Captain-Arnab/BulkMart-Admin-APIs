-- 016_product_benefits_storage.sql
-- Per-product Benefits and Storage Tips (optional TEXT, like description).

ALTER TABLE `products`
  ADD COLUMN `benefits` TEXT NULL AFTER `description`,
  ADD COLUMN `storage_tips` TEXT NULL AFTER `benefits`;
