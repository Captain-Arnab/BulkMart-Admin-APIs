-- 007_order_items_product_set_null.sql
-- Allow deleting catalog products that appear on past orders.
-- Order lines keep name/price snapshots; product_id becomes NULL.

SET NAMES utf8mb4;

ALTER TABLE `order_items`
  DROP FOREIGN KEY `fk_order_items_product`;

ALTER TABLE `order_items`
  MODIFY `product_id` INT UNSIGNED NULL;

ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL;
