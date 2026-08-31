-- 015_admin_activity_log.sql
-- Lightweight reviewable log for admin actions (e.g. customer password reset).
-- Not a full audit framework — just enough history to see who did what.

CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_user_id` INT UNSIGNED NULL,
  `admin_name` VARCHAR(120) NOT NULL DEFAULT '',
  `action` VARCHAR(80) NOT NULL,
  `entity_type` VARCHAR(40) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `note` VARCHAR(500) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin_activity_entity` (`entity_type`, `entity_id`),
  KEY `idx_admin_activity_action` (`action`),
  KEY `idx_admin_activity_created` (`created_at`),
  CONSTRAINT `fk_admin_activity_admin`
    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
