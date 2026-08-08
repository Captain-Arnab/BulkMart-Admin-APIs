-- 003_customers_blocked_and_settings.sql

ALTER TABLE `customers`
  ADD COLUMN `is_blocked` TINYINT(1) NOT NULL DEFAULT 0 AFTER `kyc_rejection_reason`,
  ADD KEY `idx_customers_is_blocked` (`is_blocked`);

CREATE TABLE IF NOT EXISTS `app_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_app_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `app_settings` (`setting_key`, `setting_value`) VALUES
  ('support_phone', '+91 98765 43210'),
  ('support_email', 'support@veggiicart.com'),
  ('company_name', 'VeggiiCart')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
