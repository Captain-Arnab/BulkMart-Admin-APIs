-- 004_api_layer.sql
-- Customer REST API: OTP, refresh tokens, cart, delivery slots

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `otp_codes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` VARCHAR(20) NOT NULL,
  `otp_hash` VARCHAR(255) NOT NULL,
  `purpose` ENUM('login','delivery','order') NOT NULL DEFAULT 'login',
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `consumed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_otp_mobile_purpose` (`mobile`, `purpose`, `created_at`),
  KEY `idx_otp_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `otp_rate_limits` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` VARCHAR(20) NOT NULL,
  `purpose` VARCHAR(40) NOT NULL DEFAULT 'login',
  `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_otp_rate_mobile` (`mobile`, `purpose`, `sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `refresh_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `revoked_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` VARCHAR(255) NULL,
  `ip_address` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_refresh_token_hash` (`token_hash`),
  KEY `idx_refresh_customer` (`customer_id`),
  KEY `idx_refresh_expires` (`expires_at`),
  CONSTRAINT `fk_refresh_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(12,2) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_customer_product` (`customer_id`, `product_id`),
  KEY `idx_cart_product` (`product_id`),
  CONSTRAINT `fk_cart_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_slots` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slot_date` DATE NOT NULL,
  `slot_label` VARCHAR(80) NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `capacity` INT UNSIGNED NOT NULL DEFAULT 50,
  `booked_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_delivery_slots_date` (`slot_date`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `app_settings` (`setting_key`, `setting_value`) VALUES
  ('delivery_fee', '0'),
  ('otp_ttl_seconds', '300'),
  ('jwt_access_ttl_seconds', '3600'),
  ('jwt_refresh_ttl_seconds', '2592000')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
