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
        self::resetCaches();
    }

    public static function resetCaches(): void
    {
        self::$manualReviewEnabledCache = null;
        if (function_exists('app_settings_reset_cache')) {
            app_settings_reset_cache();
        }
    }

    private static ?bool $manualReviewEnabledCache = null;

    public static function parseBool(?string $value): bool
    {
        if ($value === null) {
            return false;
        }
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
    }

    /** Whether new registrations require manual admin KYC approval (admin setting overrides config). */
    public static function manualReviewEnabled(): bool
    {
        if (self::$manualReviewEnabledCache !== null) {
            return self::$manualReviewEnabledCache;
        }
        $v = (new self())->get('kyc_manual_review_enabled');
        if ($v !== null) {
            self::$manualReviewEnabledCache = self::parseBool($v);
            return self::$manualReviewEnabledCache;
        }
        self::$manualReviewEnabledCache = (bool) (app_config('kyc.manual_review_enabled') ?? false);
        return self::$manualReviewEnabledCache;
    }
}
