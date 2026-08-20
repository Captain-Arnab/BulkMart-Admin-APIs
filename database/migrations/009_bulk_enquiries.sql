-- 009_bulk_enquiries.sql
-- Bulk / high-volume enquiry leads (website + future Flutter parity).

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `bulk_enquiries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NULL,
  `name` VARCHAR(120) NOT NULL,
  `business_name` VARCHAR(180) NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `required_quantity` VARCHAR(120) NOT NULL,
  `delivery_location` VARCHAR(255) NOT NULL,
  `pincode` VARCHAR(12) NOT NULL,
  `preferred_delivery_date` DATE NULL,
  `additional_requirement` TEXT NULL,
  `admin_notes` TEXT NULL,
  `status` ENUM('new','contacted','quoted','closed') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bulk_enquiries_status` (`status`),
  KEY `idx_bulk_enquiries_created` (`created_at`),
  KEY `idx_bulk_enquiries_product` (`product_id`),
  KEY `idx_bulk_enquiries_customer` (`customer_id`),
  CONSTRAINT `fk_bulk_enquiries_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_bulk_enquiries_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `app_settings` (`setting_key`, `setting_value`) VALUES
  ('bulk_enquiry_notify_email', 'veggiicart@gmail.com'),
  ('bulk_enquiry_notify_phone', '+91 8099999086')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
