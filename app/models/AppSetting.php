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
}
