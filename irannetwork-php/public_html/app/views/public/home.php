<?php $services = site_services(); ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg" aria-hidden="true">
        <div class="hero-grid"></div>
        <div class="hero-glow hero-glow-1"></div>
        <div class="hero-glow hero-glow-2"></div>
    </div>
    <div class="container hero-inner">
        <div class="hero-eyebrow">
            <span class="dot"></span>
            <span>شرکت تخصصی شبکه، سرور و امنیت</span>
        </div>
        <h1 class="hero-title">
            خدمات تخصصی <span class="text-gradient">شبکه، سرور و امنیت</span><br>
            برای کسب‌وکارهای حرفه‌ای
        </h1>
        <p class="hero-sub">
            ایران نتورک با تجربه‌ی اجرای ده‌ها پروژه‌ی سازمانی، از طراحی و نصب زیرساخت تا پشتیبانی پیوسته و امنیت، در کنار شما است.
        </p>
        <div class="hero-cta">
            <a href="/contact" class="btn btn-primary btn-lg">
                <span>دریافت مشاوره رایگان</span>
                <?= icon_svg('arrow', 20) ?>
            </a>
            <a href="tel:<?= e(defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664') ?>" class="btn btn-outline btn-lg">
                <?= icon_svg('phone', 20) ?>
                <span>تماس مستقیم</span>
            </a>
        </div>
        <div class="hero-stats">
            <div class="stat"><strong>۱۰+</strong><span>سال تجربه</span></div>
            <div class="stat"><strong>۵۰۰+</strong><span>پروژه موفق</span></div>
            <div class="stat"><strong>۲۴/۷</strong><span>پشتیبانی</span></div>
            <div class="stat"><strong>۲</strong><span>دفتر فعال</span></div>
        </div>
    </div>
</section>

<!-- ABOUT TEASER -->
<section class="section">
    <div class="container">
        <div class="about-teaser">
            <div>
                <span class="section-eyebrow">درباره ایران نتورک</span>
                <h2 class="section-title">شریک قابل اعتماد زیرساخت IT شما</h2>
                <p class="section-lead">
                    ایران نتورک یک شرکت تخصصی در حوزه‌ی خدمات شبکه، سرور، امنیت، ویپ و دیجیتال مارکتینگ است. ما با تمرکز بر کیفیت اجرا، پاسخ‌گویی سریع و پشتیبانی پایدار، زیرساخت IT شرکت‌ها و سازمان‌ها را در تهران و اصفهان مدیریت می‌کنیم.
                </p>
                <a href="/about" class="link-arrow">آشنایی بیشتر با ما <?= icon_svg('arrow', 18) ?></a>
            </div>
            <div class="about-points">
                <div class="point"><?= icon_svg('bolt', 22) ?><div><h4>پاسخ‌گویی سریع</h4><p>تیم پشتیبانی در سریع‌ترین زمان در دسترس شماست.</p></div></div>
                <div class="point"><?= icon_svg('shield', 22) ?><div><h4>اجرای استاندارد</h4><p>پایبندی به استانداردهای بین‌المللی در همه پروژه‌ها.</p></div></div>
                <div class="point"><?= icon_svg('star', 22) ?><div><h4>تجربه‌ی سازمانی</h4><p>مناسب شرکت‌ها، سازمان‌ها و کسب‌وکارهای جدی.</p></div></div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">خدمات ما</span>
            <h2 class="section-title">سرویس‌های تخصصی ایران نتورک</h2>
            <p class="section-lead">پوشش کامل نیازهای زیرساختی، ارتباطی و امنیتی کسب‌وکار شما در یک نقطه.</p>
        </div>

        <div class="services-grid">
            <?php foreach ($services as $s): ?>
            <a href="/services/<?= e($s['slug']) ?>" class="service-card">
                <span class="service-icon"><?= icon_svg($s['icon'], 28) ?></span>
                <h3><?= e($s['title']) ?></h3>
                <p><?= e($s['short']) ?></p>
                <span class="service-more">مشاهده جزئیات <?= icon_svg('arrow', 16) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WHY US -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">چرا ایران نتورک؟</span>
            <h2 class="section-title">دلایل اعتماد مشتریان به ما</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card"><span class="feature-num">۰۱</span><h4>تیم تخصصی</h4><p>کارشناسان باتجربه در حوزه شبکه، سرور و امنیت.</p></div>
            <div class="feature-card"><span class="feature-num">۰۲</span><h4>SLA شفاف</h4><p>قراردادهای روشن با تعهد سطح خدمات مشخص.</p></div>
            <div class="feature-card"><span class="feature-num">۰۳</span><h4>اجرای استاندارد</h4><p>کیفیت اجرا مطابق با استانداردهای روز دنیا.</p></div>
            <div class="feature-card"><span class="feature-num">۰۴</span><h4>پشتیبانی پایدار</h4><p>پشتیبانی ریموت و حضوری، در دو شهر اصلی.</p></div>
        </div>
    </div>
</section>

<!-- PROCESS -->
<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">فرآیند همکاری</span>
            <h2 class="section-title">از مشاوره تا پشتیبانی، در ۴ مرحله</h2>
        </div>
        <ol class="process-list">
            <li><span class="proc-num">۱</span><h4>بررسی نیاز</h4><p>تحلیل دقیق نیازها و وضعیت فعلی زیرساخت.</p></li>
            <li><span class="proc-num">۲</span><h4>طراحی راهکار</h4><p>ارائه‌ی راهکار اختصاصی، متناسب با مقیاس شما.</p></li>
            <li><span class="proc-num">۳</span><h4>اجرا</h4><p>پیاده‌سازی استاندارد توسط تیم متخصص.</p></li>
            <li><span class="proc-num">۴</span><h4>پشتیبانی</h4><p>نگهداری و پشتیبانی مستمر پس از تحویل.</p></li>
        </ol>
    </div>
</section>

<!-- LOCATIONS -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">دفاتر ما</span>
            <h2 class="section-title">در کنار شما، در تهران و اصفهان</h2>
        </div>
        <div class="locations-grid">
            <div class="location-card">
                <h3>دفتر تهران</h3>
                <p class="location-addr"><?= icon_svg('pin', 20) ?> <span><?= e(defined('CONTACT_ADDRESS_TEHRAN') ? CONTACT_ADDRESS_TEHRAN : 'تهران پارس، فلکه اول، خیابان بابا یوسفی، پلاک ۳') ?></span></p>
                <p class="location-phone"><?= icon_svg('phone', 20) ?> <a dir="ltr" href="tel:<?= e(defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664') ?>">021-91014664</a></p>
            </div>
            <div class="location-card">
                <h3>دفتر اصفهان</h3>
                <p class="location-addr"><?= icon_svg('pin', 20) ?> <span><?= e(defined('CONTACT_ADDRESS_ISFAHAN') ? CONTACT_ADDRESS_ISFAHAN : 'اصفهان، شاهین شهر، خیابان امام علی، فرعی ۲ شرقی، پلاک ۲۷') ?></span></p>
                <p class="location-phone"><?= icon_svg('phone', 20) ?> <a dir="ltr" href="tel:<?= e(defined('CONTACT_PHONE_ISFAHAN') ? CONTACT_PHONE_ISFAHAN : '03191011239') ?>">031-91011239</a></p>
            </div>
        </div>
    </div>
</section>

<!-- FINAL CTA -->
<section class="cta-band">
    <div class="container cta-inner">
        <div>
            <h2>آماده‌ی شروع پروژه‌ی شبکه‌ی شما هستیم</h2>
            <p>همین حالا تماس بگیرید و مشاوره رایگان دریافت کنید.</p>
        </div>
        <div class="cta-actions">
            <a href="/contact" class="btn btn-primary btn-lg">درخواست مشاوره</a>
            <a href="tel:<?= e(defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664') ?>" class="btn btn-outline btn-lg"><?= icon_svg('phone', 18) ?> تماس فوری</a>
        </div>
    </div>
</section>
