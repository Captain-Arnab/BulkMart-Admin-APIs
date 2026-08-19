-- 001_create_schema.sql
-- VeggiiCart core schema (InnoDB, utf8mb4)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `image_url` VARCHAR(500) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role_type` ENUM('super_admin','sub_admin','delivery_manager') NOT NULL DEFAULT 'sub_admin',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_users_email` (`email`),
  KEY `idx_admin_users_role` (`role_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(160) NOT NULL,
  `unit` VARCHAR(60) NOT NULL DEFAULT 'per kg',
  `moq` DECIMAL(12,2) NOT NULL DEFAULT 1.00,
  `price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `stock` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `image_url` VARCHAR(500) NULL,
  `batch_no` VARCHAR(80) NULL,
  `item_code` VARCHAR(80) NULL,
  `description` TEXT NULL,
  `grade` VARCHAR(40) NULL,
  `origin` VARCHAR(120) NULL,
  `in_stock` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_item_code` (`item_code`),
  KEY `idx_products_category_id` (`category_id`),
  KEY `idx_products_is_active` (`is_active`),
  KEY `idx_products_in_stock` (`in_stock`),
  KEY `idx_products_name` (`name`),
  CONSTRAINT `fk_products_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` VARCHAR(20) NOT NULL,
  `email` VARCHAR(190) NULL,
  `password_hash` VARCHAR(255) NULL,
  `business_name` VARCHAR(180) NOT NULL,
  `owner_name` VARCHAR(120) NOT NULL,
  `business_type` VARCHAR(80) NOT NULL,
  `gst_number` VARCHAR(30) NULL,
  `fssai_number` VARCHAR(40) NULL,
  `pan_number` VARCHAR(20) NULL,
  `avatar_url` VARCHAR(500) NULL,
  `kyc_status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `kyc_rejection_reason` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customers_mobile` (`mobile`),
  UNIQUE KEY `uq_customers_email` (`email`),
  KEY `idx_customers_kyc_status` (`kyc_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customer_documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `document_type` ENUM(
    'gst_certificate',
    'fssai_license',
    'pan_card',
    'aadhaar_card',
    'shop_establishment',
    'trade_license',
    'cancelled_cheque',
    'business_photo',
    'owner_photo'
  ) NOT NULL,
  `file_url` VARCHAR(500) NOT NULL,
  `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customer_documents_customer_id` (`customer_id`),
  KEY `idx_customer_documents_type` (`document_type`),
  CONSTRAINT `fk_customer_documents_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `addresses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(60) NOT NULL DEFAULT 'Shop',
  `line1` VARCHAR(255) NOT NULL,
  `line2` VARCHAR(255) NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(12) NOT NULL,
  `landmark` VARCHAR(160) NULL,
  `geo_lat` DECIMAL(10,7) NULL,
  `geo_lng` DECIMAL(10,7) NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_addresses_customer_id` (`customer_id`),
  KEY `idx_addresses_default` (`customer_id`, `is_default`),
  CONSTRAINT `fk_addresses_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(40) NOT NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `address_id` INT UNSIGNED NOT NULL,
  `status` ENUM(
    'placed',
    'confirmed',
    'delivery_date_set',
    'out_for_delivery',
    'delivered',
    'cancelled'
  ) NOT NULL DEFAULT 'placed',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` VARCHAR(40) NOT NULL DEFAULT 'COD',
  `estimated_delivery_date` DATE NULL,
  `delivered_at` DATETIME NULL,
  `assigned_delivery_manager_id` INT UNSIGNED NULL,
  `placed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_order_number` (`order_number`),
  KEY `idx_orders_customer_id` (`customer_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_placed_at` (`placed_at`),
  KEY `idx_orders_delivery_manager` (`assigned_delivery_manager_id`),
  CONSTRAINT `fk_orders_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_orders_address`
    FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_orders_delivery_manager`
    FOREIGN KEY (`assigned_delivery_manager_id`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `product_name_snapshot` VARCHAR(160) NOT NULL,
  `unit_snapshot` VARCHAR(60) NOT NULL,
  `quantity` DECIMAL(12,2) NOT NULL,
  `unit_price_snapshot` DECIMAL(12,2) NOT NULL,
  `line_total` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `idx_order_items_product_id` (`product_id`),
  CONSTRAINT `fk_order_items_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_user_id` INT UNSIGNED NOT NULL,
  `module_key` VARCHAR(60) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_permissions_user_module` (`admin_user_id`, `module_key`),
  KEY `idx_role_permissions_module` (`module_key`),
  CONSTRAINT `fk_role_permissions_admin`
    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `banners` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `image_url` VARCHAR(500) NULL,
  `title` VARCHAR(160) NULL,
  `description` TEXT NULL,
  `link` VARCHAR(500) NULL,
  `active_from` DATETIME NULL,
  `active_to` DATETIME NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_banners_active` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `offers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(160) NOT NULL,
  `discount_type` ENUM('percentage','flat') NOT NULL DEFAULT 'percentage',
  `discount_value` DECIMAL(12,2) NOT NULL,
  `min_qty` DECIMAL(12,2) NULL,
  `category_id` INT UNSIGNED NULL,
  `coupon_code` VARCHAR(60) NULL,
  `valid_from` DATETIME NULL,
  `valid_till` DATETIME NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_offers_coupon` (`coupon_code`),
  KEY `idx_offers_category_id` (`category_id`),
  KEY `idx_offers_active` (`is_active`),
  CONSTRAINT `fk_offers_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `subject_type` VARCHAR(80) NOT NULL,
  `description` TEXT NOT NULL,
  `related_order_id` INT UNSIGNED NULL,
  `status` ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_support_tickets_customer_id` (`customer_id`),
  KEY `idx_support_tickets_status` (`status`),
  KEY `idx_support_tickets_order` (`related_order_id`),
  CONSTRAINT `fk_support_tickets_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_support_tickets_order`
    FOREIGN KEY (`related_order_id`) REFERENCES `orders` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `support_ticket_replies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` INT UNSIGNED NOT NULL,
  `admin_user_id` INT UNSIGNED NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_support_ticket_replies_ticket` (`ticket_id`),
  CONSTRAINT `fk_support_replies_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_support_replies_admin`
    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `market_prices` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `effective_date` DATE NOT NULL,
  `updated_by_admin_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_market_prices_product` (`product_id`),
  KEY `idx_market_prices_date` (`effective_date`),
  CONSTRAINT `fk_market_prices_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_market_prices_admin`
    FOREIGN KEY (`updated_by_admin_id`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlists_customer_product` (`customer_id`, `product_id`),
  KEY `idx_wishlists_product` (`product_id`),
  CONSTRAINT `fk_wishlists_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_wishlists_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(180) NOT NULL,
  `body` TEXT NOT NULL,
  `type` ENUM('order','offer','verification','stock') NOT NULL,
  `related_id` INT UNSIGNED NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_customer` (`customer_id`),
  KEY `idx_notifications_unread` (`customer_id`, `is_read`),
  KEY `idx_notifications_type` (`type`),
  CONSTRAINT `fk_notifications_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
