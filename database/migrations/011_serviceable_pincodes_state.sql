-- 011_serviceable_pincodes_state.sql
-- Add state to serviceable_pincodes (default Telangana for existing Hyderabad rows).

SET NAMES utf8mb4;

ALTER TABLE `serviceable_pincodes`
  ADD COLUMN `state` VARCHAR(80) NOT NULL DEFAULT 'Telangana' AFTER `city`;

UPDATE `serviceable_pincodes`
SET `state` = 'Telangana'
WHERE `state` = '' OR `state` IS NULL;
