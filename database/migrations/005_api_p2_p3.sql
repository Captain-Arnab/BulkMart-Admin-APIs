-- 005_api_p2_p3.sql
-- FAQs, cart coupons, order discount columns

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `category` VARCHAR(80) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_faqs_active` (`is_active`, `sort_order`),
  KEY `idx_faqs_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cart_meta` (
  `customer_id` INT UNSIGNED NOT NULL,
  `coupon_code` VARCHAR(60) NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`),
  CONSTRAINT `fk_cart_meta_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `orders`
  ADD COLUMN `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `delivery_fee`,
  ADD COLUMN `coupon_code` VARCHAR(60) NULL AFTER `discount_amount`;

INSERT INTO `faqs` (`question`, `answer`, `category`, `sort_order`, `is_active`) VALUES
  ('How do I place an order?', 'Browse products, add items to cart (respecting MOQ), choose a delivery address, and place a COD order.', 'Orders', 1, 1),
  ('What is MOQ?', 'Minimum Order Quantity — the smallest quantity you can order for a product.', 'Products', 2, 1),
  ('When can I cancel an order?', 'You can cancel before the order is out for delivery (placed, confirmed, or delivery date set).', 'Orders', 3, 1),
  ('How does KYC verification work?', 'Submit your business details and documents. Our team reviews and approves or rejects with a reason.', 'Account', 4, 1),
  ('What payment methods are supported?', 'Currently Cash on Delivery (COD) is supported for B2B orders.', 'Payments', 5, 1);
