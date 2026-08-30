-- Replace the double-negative `require_kyc_approved` checkout gate with a clearer
-- registration-time setting: `kyc_manual_review_enabled` (off by default).
-- Preserve whatever the old flag currently implies, then drop it so only one
-- setting controls this behavior.
INSERT INTO `app_settings` (`setting_key`, `setting_value`)
SELECT 'kyc_manual_review_enabled', COALESCE(
    (SELECT setting_value FROM `app_settings` WHERE `setting_key` = 'require_kyc_approved'),
    '0'
)
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

DELETE FROM `app_settings` WHERE `setting_key` = 'require_kyc_approved';
