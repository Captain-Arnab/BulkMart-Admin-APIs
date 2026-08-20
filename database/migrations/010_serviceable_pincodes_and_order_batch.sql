-- 010_serviceable_pincodes_and_order_batch.sql
-- Hyderabad serviceable pincodes + multi-address checkout batch_id on orders.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `serviceable_pincodes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pincode` CHAR(6) NOT NULL,
  `city` VARCHAR(80) NOT NULL DEFAULT 'Hyderabad',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_serviceable_pincodes_pincode` (`pincode`),
  KEY `idx_serviceable_pincodes_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `serviceable_pincodes` (`pincode`, `city`, `is_active`) VALUES
('500001','Hyderabad',1),('500002','Hyderabad',1),('500003','Hyderabad',1),
('500004','Hyderabad',1),('500005','Hyderabad',1),('500006','Hyderabad',1),
('500007','Hyderabad',1),('500008','Hyderabad',1),('500009','Hyderabad',1),
('500010','Hyderabad',1),('500011','Hyderabad',1),('500012','Hyderabad',1),
('500013','Hyderabad',1),('500014','Hyderabad',1),('500015','Hyderabad',1),
('500016','Hyderabad',1),('500017','Hyderabad',1),('500018','Hyderabad',1),
('500019','Hyderabad',1),('500020','Hyderabad',1),('500022','Hyderabad',1),
('500023','Hyderabad',1),('500024','Hyderabad',1),('500025','Hyderabad',1),
('500026','Hyderabad',1),('500027','Hyderabad',1),('500028','Hyderabad',1),
('500029','Hyderabad',1),('500030','Hyderabad',1),('500031','Hyderabad',1),
('500032','Hyderabad',1),('500033','Hyderabad',1),('500034','Hyderabad',1),
('500035','Hyderabad',1),('500036','Hyderabad',1),('500037','Hyderabad',1),
('500038','Hyderabad',1),('500039','Hyderabad',1),('500040','Hyderabad',1),
('500041','Hyderabad',1),('500042','Hyderabad',1),('500043','Hyderabad',1),
('500044','Hyderabad',1),('500045','Hyderabad',1),('500046','Hyderabad',1),
('500047','Hyderabad',1),('500048','Hyderabad',1),('500049','Hyderabad',1),
('500050','Hyderabad',1),('500051','Hyderabad',1),('500052','Hyderabad',1),
('500053','Hyderabad',1),('500054','Hyderabad',1),('500055','Hyderabad',1),
('500056','Hyderabad',1),('500057','Hyderabad',1),('500058','Hyderabad',1),
('500059','Hyderabad',1),('500060','Hyderabad',1),('500061','Hyderabad',1),
('500062','Hyderabad',1),('500063','Hyderabad',1),('500064','Hyderabad',1),
('500065','Hyderabad',1),('500066','Hyderabad',1),('500067','Hyderabad',1),
('500068','Hyderabad',1),('500069','Hyderabad',1),('500070','Hyderabad',1),
('500072','Hyderabad',1),('500073','Hyderabad',1),('500074','Hyderabad',1),
('500075','Hyderabad',1),('500076','Hyderabad',1),('500077','Hyderabad',1),
('500078','Hyderabad',1),('500079','Hyderabad',1),('500080','Hyderabad',1),
('500081','Hyderabad',1),('500082','Hyderabad',1),('500083','Hyderabad',1),
('500084','Hyderabad',1),('500085','Hyderabad',1),('500086','Hyderabad',1),
('500087','Hyderabad',1),('500088','Hyderabad',1),('500089','Hyderabad',1),
('500090','Hyderabad',1),('500091','Hyderabad',1),('500092','Hyderabad',1),
('500093','Hyderabad',1),('500094','Hyderabad',1),('500095','Hyderabad',1),
('500096','Hyderabad',1),('500097','Hyderabad',1),('500098','Hyderabad',1),
('500100','Hyderabad',1),('500101','Hyderabad',1),('500102','Hyderabad',1),
('500103','Hyderabad',1),('500104','Hyderabad',1),('500106','Hyderabad',1),
('500107','Hyderabad',1),('500108','Hyderabad',1),('500110','Hyderabad',1),
('500111','Hyderabad',1),('500112','Hyderabad',1),('500113','Hyderabad',1),
('500114','Hyderabad',1),('500115','Hyderabad',1),('500117','Hyderabad',1),
('500118','Hyderabad',1),('500119','Hyderabad',1)
ON DUPLICATE KEY UPDATE `city` = VALUES(`city`), `is_active` = VALUES(`is_active`);

ALTER TABLE `orders`
  ADD COLUMN `batch_id` CHAR(36) NULL AFTER `coupon_code`,
  ADD KEY `idx_orders_batch_id` (`batch_id`);
