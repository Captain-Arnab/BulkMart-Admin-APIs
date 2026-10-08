-- 018_admin_users_mobile.sql
-- Optional mobile for admin accounts; a delivery manager's number pre-fills the "Share via WhatsApp" recipient.
-- Existing rows stay NULL.
-- Safe to re-run; works on MySQL and MariaDB (MySQL has no ADD COLUMN IF NOT EXISTS).

SET NAMES utf8mb4;

SET @vc_col_exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_users' AND COLUMN_NAME = 'mobile');
SET @vc_sql := IF(@vc_col_exists = 0, 'ALTER TABLE `admin_users` ADD COLUMN `mobile` VARCHAR(15) NULL DEFAULT NULL AFTER `email`', 'SELECT ''mobile already exists'' AS info');
PREPARE vc_stmt FROM @vc_sql;
EXECUTE vc_stmt;
DEALLOCATE PREPARE vc_stmt;
