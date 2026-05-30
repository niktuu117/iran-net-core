<?php
declare(strict_types=1);

/**
 * Global helper functions.
 */

if (!function_exists('e')) {
    /** Escape output for HTML context. */
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /** Build an absolute URL relative to the site root. */
    function url(string $path = '/'): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /** Build a URL for an asset under /assets. */
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
    /** Return 'active' if the current path matches. */
    function is_active(string $path, bool $exact = true): string
    {
        $cur = current_path();
        if ($exact) {
            return $cur === $path ? 'active' : '';
        }
        return str_starts_with($cur, $path) ? 'active' : '';
    }
}

if (!function_exists('site_services')) {
    /**
     * Canonical list of services.
     * In Phase 3 this will be loaded from the database.
     *
     * @return array<int, array{slug:string,title:string,short:string,icon:string}>
     */
    function site_services(): array
    {
        return [
            ['slug' => 'network-support',      'title' => 'پشتیبانی شبکه',            'short' => 'پشتیبانی پیوسته و حرفه‌ای زیرساخت شبکه شرکت‌ها و سازمان‌ها.', 'icon' => 'support'],
            ['slug' => 'network-installation', 'title' => 'نصب و راه‌اندازی شبکه',     'short' => 'طراحی، اجرا و راه‌اندازی شبکه‌های سیمی و بی‌سیم سازمانی.',    'icon' => 'install'],
            ['slug' => 'voip',                 'title' => 'ویپ و سانترال',            'short' => 'پیاده‌سازی سیستم‌های تلفنی VoIP و سانترال تحت شبکه.',        'icon' => 'voip'],
            ['slug' => 'digital-marketing',    'title' => 'دیجیتال مارکتینگ',         'short' => 'سئو، تبلیغات و طراحی سایت برای کسب‌وکارهای جدی.',           'icon' => 'marketing'],
            ['slug' => 'network-security',     'title' => 'امنیت شبکه و سرور',        'short' => 'تأمین امنیت زیرساخت شبکه، فایروال، VPN و سرور.',           'icon' => 'security'],
            ['slug' => 'server-support',       'title' => 'پشتیبانی سرور',            'short' => 'نگهداری، مانیتورینگ و پشتیبانی تخصصی سرورهای سازمانی.',     'icon' => 'server'],
            ['slug' => 'active-network',       'title' => 'خدمات اکتیو شبکه',         'short' => 'نصب و پیکربندی تجهیزات اکتیو مانند سوئیچ، روتر و فایروال.', 'icon' => 'active'],
            ['slug' => 'passive-network',      'title' => 'خدمات پسیو شبکه',          'short' => 'کابل‌کشی، فیبر نوری و اجرای استاندارد زیرساخت پسیو.',       'icon' => 'passive'],
        ];
    }
}

if (!function_exists('icon_svg')) {
    /** Inline SVG icons keyed by name. Lightweight, no external dependency. */
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
        ];
        $body = $icons[$name] ?? $icons['check'];
        return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $body . '</svg>';
    }
}
