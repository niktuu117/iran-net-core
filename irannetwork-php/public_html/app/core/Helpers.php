<?php
declare(strict_types=1);

/**
 * Global helper functions (Phase 2).
 */

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, int $status = 302): void
    {
        header('Location: ' . $path, true, $status);
        exit;
    }
}

if (!function_exists('url')) {
    function url(string $path = '/'): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('site_url')) {
    /** Absolute site URL (no trailing slash). Falls back to APP_URL or scheme+host. */
    function site_url(string $path = '/'): string
    {
        $base = site_setting('site_url', defined('APP_URL') ? APP_URL : '');
        if (!$base) {
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base   = $scheme . '://' . $host;
        }
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('admin_url')) {
    function admin_url(string $path = ''): string
    {
        return '/admin/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('current_path')) {
    function current_path(): string
    {
        $url = isset($_GET['_url']) ? '/' . trim((string)$_GET['_url'], '/') : '/';
        return $url === '' ? '/' : $url;
    }
}

if (!function_exists('is_active')) {
    function is_active(string $path, bool $exact = true): string
    {
        $cur = current_path();
        if ($exact) return $cur === $path ? 'active' : '';
        return str_starts_with($cur, $path) ? 'active' : '';
    }
}

if (!function_exists('slugify')) {
    /**
     * Build a URL slug. Persian characters are kept as-is (URL-safe in UTF-8),
     * spaces become dashes, ASCII is lower-cased.
     */
    function slugify(string $text): string
    {
        $text = trim($text);
        // Replace whitespace and common separators with dash
        $text = preg_replace('/[\s_\-\/\\\\]+/u', '-', $text) ?? $text;
        // Remove characters that are unsafe in URLs (keep letters, digits, dash, Persian/Arabic ranges)
        $text = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $text) ?? $text;
        $text = preg_replace('/-+/', '-', $text) ?? $text;
        $text = trim($text, '-');
        // Lowercase ASCII portion only
        $text = mb_strtolower($text, 'UTF-8');
        return $text === '' ? 'item' : $text;
    }
}

if (!function_exists('unique_slug')) {
    /**
     * Ensure slug is unique within a table (column `slug`). Appends -2, -3, ...
     */
    function unique_slug(string $slug, string $table, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;
        while (true) {
            $candidate = $i === 1 ? $base : ($base . '-' . $i);
            $sql = "SELECT id FROM `{$table}` WHERE slug = ?" . ($ignoreId ? ' AND id <> ?' : '') . ' LIMIT 1';
            $params = [$candidate];
            if ($ignoreId) $params[] = $ignoreId;
            $row = Database::fetch($sql, $params);
            if (!$row) return $candidate;
            $i++;
        }
    }
}

if (!function_exists('reserved_slugs')) {
    /** Slugs that conflict with system routes / directories. */
    function reserved_slugs(): array
    {
        return ['admin','app','assets','blog','cache','category','contact','database',
                'sitemap.xml','robots.txt','services','tag','uploads','404'];
    }
}

if (!function_exists('is_reserved_slug')) {
    function is_reserved_slug(string $slug): bool
    {
        return in_array(strtolower(trim($slug)), reserved_slugs(), true);
    }
}

if (!function_exists('format_date_fa')) {
    function format_date_fa(?string $datetime): string
    {
        if (!$datetime) return '-';
        $ts = strtotime($datetime);
        if (!$ts) return '-';
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return str_replace($en, $fa, date('Y/m/d H:i', $ts));
    }
}

if (!function_exists('excerpt')) {
    function excerpt(?string $text, int $words = 30): string
    {
        $text = trim(strip_tags((string)$text));
        if ($text === '') return '';
        $parts = preg_split('/\s+/u', $text) ?: [];
        if (count($parts) <= $words) return $text;
        return implode(' ', array_slice($parts, 0, $words)) . '…';
    }
}

if (!function_exists('sanitize_html')) {
    /**
     * Basic HTML sanitizer: strips <script>, <style>, <iframe> and on* event attributes.
     * Allowed tags = whitelist below.
     */
    function sanitize_html(string $html): string
    {
        $allowed = '<p><br><strong><b><em><i><u><a><ul><ol><li><h2><h3><h4><blockquote><img><figure><figcaption><pre><code><hr><div><span>';
        $clean = strip_tags($html, $allowed);
        // Remove on* event attributes and javascript: URLs
        $clean = preg_replace('/\son\w+\s*=\s*"(?:[^"\\\\]|\\\\.)*"/i', '', $clean) ?? $clean;
        $clean = preg_replace("/\son\w+\s*=\s*'(?:[^'\\\\]|\\\\.)*'/i", '', $clean) ?? $clean;
        $clean = preg_replace('/\son\w+\s*=\s*[^\s>]+/i', '', $clean) ?? $clean;
        $clean = preg_replace('/javascript\s*:/i', '', $clean) ?? $clean;
        return $clean;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $value = null): ?string
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $v = $_SESSION['_flash'][$key] ?? null;
        if ($v !== null) unset($_SESSION['_flash'][$key]);
        return $v;
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return isset($_SESSION['_old'][$key]) ? (string)$_SESSION['_old'][$key] : $default;
    }
}

if (!function_exists('keep_old')) {
    function keep_old(array $data): void
    {
        $_SESSION['_old'] = $data;
    }
}

if (!function_exists('clear_old')) {
    function clear_old(): void
    {
        unset($_SESSION['_old']);
    }
}

if (!function_exists('site_setting')) {
    /**
     * Read a setting value from DB (cached). Falls back to $default.
     */
    function site_setting(string $key, ?string $default = null): ?string
    {
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            try {
                if (Database::isConfigured()) {
                    foreach (Database::fetchAll('SELECT setting_key, setting_value FROM site_settings') as $row) {
                        $cache[$row['setting_key']] = $row['setting_value'];
                    }
                }
            } catch (Throwable $e) {
                $cache = [];
            }
        }
        return array_key_exists($key, $cache) ? $cache[$key] : $default;
    }
}

if (!function_exists('site_services')) {
    /**
     * Canonical service list. Tries DB first, falls back to static array.
     * @return array<int, array{slug:string,title:string,short:string,icon:string}>
     */
    function site_services(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $iconMap = [
            'network-support'=>'support','network-installation'=>'install','voip'=>'voip',
            'digital-marketing'=>'marketing','network-security'=>'security','server-support'=>'server',
            'active-network'=>'active','passive-network'=>'passive',
        ];

        try {
            if (Database::isConfigured()) {
                $rows = Database::fetchAll(
                    "SELECT slug, title, excerpt FROM services WHERE status='published' ORDER BY sort_order ASC, id ASC"
                );
                if ($rows) {
                    $cache = array_map(fn($r) => [
                        'slug'  => $r['slug'],
                        'title' => $r['title'],
                        'short' => (string)($r['excerpt'] ?? ''),
                        'icon'  => $iconMap[$r['slug']] ?? 'check',
                    ], $rows);
                    return $cache;
                }
            }
        } catch (Throwable $e) {
            // fall through to static list
        }

        $cache = [
            ['slug'=>'network-support','title'=>'پشتیبانی شبکه','short'=>'پشتیبانی پیوسته و حرفه‌ای زیرساخت شبکه شرکت‌ها و سازمان‌ها.','icon'=>'support'],
            ['slug'=>'network-installation','title'=>'نصب و راه‌اندازی شبکه','short'=>'طراحی، اجرا و راه‌اندازی شبکه‌های سیمی و بی‌سیم سازمانی.','icon'=>'install'],
            ['slug'=>'voip','title'=>'ویپ و سانترال','short'=>'پیاده‌سازی سیستم‌های تلفنی VoIP و سانترال تحت شبکه.','icon'=>'voip'],
            ['slug'=>'digital-marketing','title'=>'دیجیتال مارکتینگ','short'=>'سئو، تبلیغات و طراحی سایت برای کسب‌وکارهای جدی.','icon'=>'marketing'],
            ['slug'=>'network-security','title'=>'امنیت شبکه و سرور','short'=>'تأمین امنیت زیرساخت شبکه، فایروال، VPN و سرور.','icon'=>'security'],
            ['slug'=>'server-support','title'=>'پشتیبانی سرور','short'=>'نگهداری، مانیتورینگ و پشتیبانی تخصصی سرورهای سازمانی.','icon'=>'server'],
            ['slug'=>'active-network','title'=>'خدمات اکتیو شبکه','short'=>'نصب و پیکربندی تجهیزات اکتیو مانند سوئیچ، روتر و فایروال.','icon'=>'active'],
            ['slug'=>'passive-network','title'=>'خدمات پسیو شبکه','short'=>'کابل‌کشی، فیبر نوری و اجرای استاندارد زیرساخت پسیو.','icon'=>'passive'],
        ];
        return $cache;
    }
}

if (!function_exists('social_links')) {
    /**
     * Returns active social links, keyed by platform.
     * Each entry: ['url','label','icon'].
     */
    function social_links(): array
    {
        $defs = [
            'instagram' => ['label'=>'اینستاگرام', 'icon'=>'instagram'],
            'telegram'  => ['label'=>'تلگرام',     'icon'=>'telegram'],
            'whatsapp'  => ['label'=>'واتساپ',     'icon'=>'whatsapp'],
            'linkedin'  => ['label'=>'لینکدین',    'icon'=>'linkedin'],
            'aparat'    => ['label'=>'آپارات',     'icon'=>'aparat'],
            'youtube'   => ['label'=>'یوتیوب',     'icon'=>'youtube'],
            'twitter'   => ['label'=>'توییتر/X',   'icon'=>'twitter'],
        ];
        $out = [];
        foreach ($defs as $key => $meta) {
            $val = trim((string)site_setting('social_' . $key, ''));
            if ($val === '') {
                // legacy keys from Phase 2 settings
                $val = trim((string)site_setting($key, ''));
            }
            if ($val === '') continue;
            // Build URL — whatsapp/telegram may be raw phone numbers
            $url = $val;
            if ($key === 'whatsapp' && !preg_match('#^https?://#i', $url)) {
                $url = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $url);
            }
            if ($key === 'telegram' && !preg_match('#^https?://#i', $url)) {
                $url = 'https://t.me/' . ltrim($url, '@');
            }
            $out[$key] = ['url'=>$url, 'label'=>$meta['label'], 'icon'=>$meta['icon']];
        }
        return $out;
    }
}

if (!function_exists('office_locations')) {
    /**
     * Returns office locations with map URLs.
     * @return array<int,array{key:string,title:string,address:string,lat:?string,lng:?string,map_url:?string}>
     */
    function office_locations(): array
    {
        $out = [];
        foreach (['tehran','isfahan'] as $k) {
            $title = site_setting("office_{$k}_title", $k === 'tehran' ? 'دفتر تهران' : 'دفتر اصفهان');
            $addr  = site_setting("office_{$k}_address", site_setting('address_' . $k, ''));
            $lat   = site_setting("office_{$k}_lat", '');
            $lng   = site_setting("office_{$k}_lng", '');
            $map   = null;
            if ($lat !== '' && $lng !== '') {
                $map = 'https://www.google.com/maps?q=' . rawurlencode($lat) . ',' . rawurlencode($lng);
            }
            if ($addr !== '' || $map) {
                $out[] = ['key'=>$k,'title'=>$title,'address'=>(string)$addr,'lat'=>$lat,'lng'=>$lng,'map_url'=>$map];
            }
        }
        return $out;
    }
}


if (!function_exists('icon_svg')) {
    function icon_svg(string $name, int $size = 28): string
    {
        $icons = [
            'support'  => '<path d="M21 12a9 9 0 1 0-18 0v5a2 2 0 0 0 2 2h2v-7H5a7 7 0 1 1 14 0h-2v7h2a2 2 0 0 0 2-2z"/>',
            'install'  => '<path d="M4 7h16M4 12h16M4 17h10"/><circle cx="18" cy="17" r="2"/>',
            'voip'     => '<path d="M22 16.92V21a1 1 0 0 1-1.1 1 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 3.18 4.1 1 1 0 0 1 4.17 3h4.09a1 1 0 0 1 1 .75 12.84 12.84 0 0 0 .7 2.81 1 1 0 0 1-.23 1L8 9.09a16 16 0 0 0 6 6l1.5-1.71a1 1 0 0 1 1-.23 12.84 12.84 0 0 0 2.81.7 1 1 0 0 1 .75 1.02z"/>',
            'marketing'=> '<path d="M3 3v18h18"/><path d="M7 15l4-4 4 4 6-6"/>',
            'security' => '<path d="M12 2 4 5v7c0 5 3.5 9 8 10 4.5-1 8-5 8-10V5l-8-3z"/><path d="m9 12 2 2 4-4"/>',
            'server'   => '<rect x="3" y="4" width="18" height="6" rx="1"/><rect x="3" y="14" width="18" height="6" rx="1"/><path d="M7 7h.01M7 17h.01"/>',
            'active'   => '<path d="M5 12h14M12 5v14M7 7l10 10M17 7 7 17"/>',
            'passive'  => '<path d="M4 6h16M4 12h16M4 18h16"/><circle cx="6" cy="6" r="1"/><circle cx="6" cy="12" r="1"/><circle cx="6" cy="18" r="1"/>',
            'check'    => '<path d="M20 6 9 17l-5-5"/>',
            'arrow'    => '<path d="M5 12h14M13 5l7 7-7 7"/>',
            'phone'    => '<path d="M22 16.92V21a1 1 0 0 1-1.1 1 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 3.18 4.1 1 1 0 0 1 4.17 3h4.09a1 1 0 0 1 1 .75 12.84 12.84 0 0 0 .7 2.81 1 1 0 0 1-.23 1L8 9.09a16 16 0 0 0 6 6l1.5-1.71a1 1 0 0 1 1-.23 12.84 12.84 0 0 0 2.81.7 1 1 0 0 1 .75 1.02z"/>',
            'mail'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/>',
            'pin'      => '<path d="M12 22s8-7.58 8-13a8 8 0 1 0-16 0c0 5.42 8 13 8 13z"/><circle cx="12" cy="9" r="3"/>',
            'clock'    => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
            'shield'   => '<path d="M12 2 4 5v7c0 5 3.5 9 8 10 4.5-1 8-5 8-10V5l-8-3z"/>',
            'bolt'     => '<path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z"/>',
            'star'     => '<path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/>',
            'edit'     => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>',
            'trash'    => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/>',
            'plus'     => '<path d="M12 5v14M5 12h14"/>',
            'logout'   => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
            'menu'     => '<path d="M3 6h18M3 12h18M3 18h18"/>',
            'inbox'    => '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
            'image'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',
            'tag'      => '<path d="M20.59 13.41 13.41 20.59a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7" y2="7"/>',
            'folder'   => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h0a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h0a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v0a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
            'doc'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
            'instagram'=> '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>',
            'telegram' => '<path d="M22 3 2 11l6 2 2 6 4-4 6 4z"/>',
            'whatsapp' => '<path d="M20 12a8 8 0 1 0-3.2 6.4L21 21l-2.6-4.2A8 8 0 0 0 20 12z"/><path d="M8.5 9.5c.3-.5 1-1 1.5-1s1 .2 1.3.7l.5 1c.2.4 0 .9-.3 1.2l-.5.4c.5 1 1.4 1.9 2.4 2.4l.4-.5c.3-.3.8-.5 1.2-.3l1 .5c.5.3.7.8.7 1.3s-.5 1.2-1 1.5c-1 .6-2.3.3-3.5-.4-1.4-.8-2.7-2.1-3.5-3.5-.7-1.2-1-2.5-.4-3.5z"/>',
            'linkedin' => '<path d="M4 4h4v4H4zM4 10h4v10H4zM10 10h4v2c.7-1.3 2-2.2 3.5-2.2 2.5 0 4.5 2 4.5 4.5V20h-4v-4.5c0-1-.5-2-1.7-2-1.2 0-2.3.9-2.3 2.2V20h-4z"/>',
            'aparat'   => '<circle cx="12" cy="12" r="10"/><path d="M8 10a2 2 0 1 1 4 0 2 2 0 1 1-4 0M12 14a2 2 0 1 1 4 0 2 2 0 1 1-4 0M10 17a2 2 0 1 1 4 0 2 2 0 1 1-4 0M14 8a2 2 0 1 1 4 0 2 2 0 1 1-4 0"/>',
            'youtube'  => '<path d="M22 8c-.2-1.5-1-2.4-2.5-2.7C17.4 5 12 5 12 5s-5.4 0-7.5.3C3 5.6 2.2 6.5 2 8c-.2 1.4-.2 4 0 5.4.2 1.5 1 2.4 2.5 2.7 2.1.3 7.5.3 7.5.3s5.4 0 7.5-.3c1.5-.3 2.3-1.2 2.5-2.7.2-1.4.2-4 0-5.4z"/><path d="m10 14 5-3-5-3z" fill="currentColor" stroke="none"/>',
            'twitter'  => '<path d="M22 6c-.7.3-1.5.5-2.3.6.8-.5 1.5-1.3 1.8-2.2-.8.5-1.7.8-2.6 1A4.1 4.1 0 0 0 12 9.5c0 .3 0 .6.1.9-3.4-.2-6.5-1.8-8.5-4.3-.4.6-.6 1.3-.6 2.1 0 1.4.7 2.7 1.8 3.4-.6 0-1.2-.2-1.8-.5v.1c0 2 1.4 3.6 3.3 4-.4.1-.8.2-1.2.2-.3 0-.6 0-.8-.1.6 1.6 2.1 2.8 4 2.8-1.4 1.1-3.3 1.8-5.2 1.8H2A11.6 11.6 0 0 0 8.3 22c7.6 0 11.8-6.3 11.8-11.8v-.5c.8-.6 1.5-1.3 2-2.2z"/>',
        ];

        $body = $icons[$name] ?? $icons['check'];
        return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $body . '</svg>';
    }
}
