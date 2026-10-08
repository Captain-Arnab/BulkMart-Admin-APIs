-- 018_admin_users_mobile.sql
-- Optional mobile for admin accounts; a delivery manager's number pre-fills the "Share via WhatsApp" recipient.
-- Existing rows stay NULL.

SET NAMES utf8mb4;

ALTER TABLE `admin_users`
  ADD COLUMN `mobile` VARCHAR(15) NULL DEFAULT NULL AFTER `email`;
