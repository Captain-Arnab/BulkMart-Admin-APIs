-- 008_banner_description.sql
-- Optional title/image/description on home banners.

SET NAMES utf8mb4;

ALTER TABLE `banners`
  MODIFY `image_url` VARCHAR(500) NULL,
  MODIFY `title` VARCHAR(160) NULL;

ALTER TABLE `banners`
  ADD COLUMN IF NOT EXISTS `description` TEXT NULL AFTER `title`;
