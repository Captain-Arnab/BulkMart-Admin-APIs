-- 006_product_images.sql
-- Multiple images per product. products.image_url stays as the denormalized primary.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image_url` VARCHAR(500) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product` (`product_id`, `sort_order`),
  KEY `idx_product_images_primary` (`product_id`, `is_primary`),
  CONSTRAINT `fk_product_images_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`, `is_primary`)
SELECT p.`id`, p.`image_url`, 0, 1
FROM `products` p
LEFT JOIN `product_images` pi ON pi.`product_id` = p.`id`
WHERE p.`image_url` IS NOT NULL
  AND p.`image_url` <> ''
  AND pi.`id` IS NULL;
