<?php

class AppSetting extends Model
{
    public function allKeyed(): array
    {
        $rows = $this->fetchAll('SELECT setting_key, setting_value FROM app_settings');
        $out = [];
        foreach ($rows as $r) {
            $out[$r['setting_key']] = $r['setting_value'];
        }
        return $out;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $row = $this->fetchOne('SELECT setting_value FROM app_settings WHERE setting_key = ?', [$key]);
        if (!$row) {
            return $default;
        }
        return $row['setting_value'] !== null ? (string) $row['setting_value'] : $default;
    }

    public function set(string $key, ?string $value): void
    {
        $existing = $this->fetchOne('SELECT id FROM app_settings WHERE setting_key = ?', [$key]);
        if ($existing) {
            $this->execute('UPDATE app_settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
        } else {
            $this->execute('INSERT INTO app_settings (setting_key, setting_value) VALUES (?,?)', [$key, $value]);
        }
    }

    public static function parseBool(?string $value): bool
    {
        if ($value === null) {
            return false;
        }
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
    }

    /** Whether customers need approved KYC before placing orders (admin setting overrides config). */
    public static function requireKycApproved(): bool
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $v = (new self())->get('require_kyc_approved');
        if ($v !== null) {
            $cached = self::parseBool($v);
            return $cached;
        }
        $cached = (bool) (app_config('checkout.require_kyc_approved') ?? false);
        return $cached;
    }
}
