-- 017_order_edit_count.sql
-- Number of customer edits (PUT /api/v1/orders/{id}). The edit rule (max count + time window) lives in
-- Order::ORDER_EDIT_MAX_COUNT / Order::ORDER_EDIT_WINDOW_SECONDS.
-- Safe to re-run; works on MySQL and MariaDB (MySQL has no ADD COLUMN IF NOT EXISTS).

SET NAMES utf8mb4;

SET @vc_col_exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'edit_count');
SET @vc_sql := IF(@vc_col_exists = 0, 'ALTER TABLE `orders` ADD COLUMN `edit_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `status`', 'SELECT ''edit_count already exists'' AS info');
PREPARE vc_stmt FROM @vc_sql;
EXECUTE vc_stmt;
DEALLOCATE PREPARE vc_stmt;
