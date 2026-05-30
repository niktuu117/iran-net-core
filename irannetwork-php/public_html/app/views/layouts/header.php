<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a href="/" class="brand" aria-label="ایران نتورک">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 32 32" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 22V10l12 8 12-8v12"/>
                    <circle cx="16" cy="16" r="2" fill="currentColor" stroke="none"/>
                </svg>
            </span>
            <span class="brand-text">
                <span class="brand-fa">ایران نتورک</span>
                <span class="brand-en">IranNetwork</span>
            </span>
        </a>

        <nav class="primary-nav" id="primaryNav" aria-label="ناوبری اصلی">
            <ul>
                <li><a href="/"        class="<?= is_active('/') ?>">خانه</a></li>
                <li><a href="/services" class="<?= is_active('/services', false) ?>">خدمات</a></li>
                <li><a href="/blog"    class="<?= is_active('/blog', false) ?>">مقالات</a></li>
                <li><a href="/about"   class="<?= is_active('/about') ?>">درباره ما</a></li>
                <li><a href="/contact" class="<?= is_active('/contact') ?>">تماس با ما</a></li>
            </ul>
        </nav>

        <div class="header-cta">
            <a href="tel:<?= e(defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664') ?>" class="btn btn-ghost btn-sm hide-mobile">
                <?= icon_svg('phone', 18) ?>
                <span>02191014664</span>
            </a>
            <a href="/contact" class="btn btn-primary btn-sm">دریافت مشاوره</a>
            <button class="nav-toggle" id="navToggle" aria-label="باز کردن منو" aria-expanded="false" aria-controls="primaryNav">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
