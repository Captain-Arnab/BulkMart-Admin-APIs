-- Admin-toggleable KYC approval gate (off by default; admin can enable in Settings).
INSERT INTO `app_settings` (`setting_key`, `setting_value`) VALUES
  ('require_kyc_approved', '0')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
