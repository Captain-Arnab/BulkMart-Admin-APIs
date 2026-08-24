-- 012_delivery_otp.sql
-- Customer handover OTP: generated when order goes out_for_delivery; required to mark delivered.

SET NAMES utf8mb4;

ALTER TABLE `orders`
  ADD COLUMN `delivery_otp` CHAR(6) NULL DEFAULT NULL AFTER `delivered_at`,
  ADD COLUMN `delivery_otp_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `delivery_otp`,
  ADD COLUMN `delivery_otp_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `delivery_otp_verified`,
  ADD COLUMN `delivery_otp_expires_at` DATETIME NULL DEFAULT NULL AFTER `delivery_otp_attempts`;
