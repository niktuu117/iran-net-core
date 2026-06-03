<?php
declare(strict_types=1);

class SiteSetting extends BaseModel
{
    protected string $table = 'site_settings';
    protected array $fillable = ['setting_key','setting_value','setting_type'];

    public function get(string $key, ?string $default = null): ?string
    {
        $row = Database::fetch('SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1', [$key]);
        return $row ? $row['setting_value'] : $default;
    }

    public function set(string $key, ?string $value, string $type = 'text'): void
    {
        $existing = Database::fetch('SELECT id FROM site_settings WHERE setting_key = ? LIMIT 1', [$key]);
        if ($existing) {
            Database::execute('UPDATE site_settings SET setting_value = ?, setting_type = ? WHERE id = ?', [$value, $type, $existing['id']]);
        } else {
            Database::execute('INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES (?,?,?)', [$key, $value, $type]);
        }
    }

    public function allAsMap(): array
    {
        $out = [];
        foreach (Database::fetchAll('SELECT setting_key, setting_value FROM site_settings') as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }
}
