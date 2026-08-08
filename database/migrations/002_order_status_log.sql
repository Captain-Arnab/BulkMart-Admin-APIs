-- 002_order_status_log.sql
-- Status change history for orders

CREATE TABLE IF NOT EXISTS `order_status_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `status` ENUM(
    'placed',
    'confirmed',
    'delivery_date_set',
    'out_for_delivery',
    'delivered',
    'cancelled'
  ) NOT NULL,
  `changed_by_admin_id` INT UNSIGNED NULL,
  `note` VARCHAR(255) NULL,
  `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_status_log_order` (`order_id`),
  KEY `idx_order_status_log_changed_at` (`changed_at`),
  CONSTRAINT `fk_order_status_log_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_order_status_log_admin`
    FOREIGN KEY (`changed_by_admin_id`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
