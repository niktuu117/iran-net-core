<section class="page-header">
    <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
            <a href="/">خانه</a> <span>/</span>
            <a href="/services">خدمات</a> <span>/</span>
            <span><?= e($service['title']) ?></span>
        </nav>
        <h1><?= e($service['h1']) ?></h1>
        <p><?= e($service['intro']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container service-detail-grid">
        <div>
            <h2 class="section-title">مزایا و ویژگی‌ها</h2>
            <ul class="benefits-list">
                <?php foreach ($service['benefits'] as $b): ?>
                <li><?= icon_svg('check', 20) ?> <span><?= e($b) ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <aside class="contact-aside">
            <h3>درخواست مشاوره</h3>
            <p>برای اطلاع از جزئیات و قیمت <?= e($service['title']) ?> با ما در تماس باشید.</p>
            <a href="/contact" class="btn btn-primary btn-block">ارسال درخواست</a>
            <a href="tel:<?= e(defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664') ?>" class="btn btn-outline btn-block"><?= icon_svg('phone', 18) ?> 021-91014664</a>
        </aside>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">فرآیند اجرا</span>
            <h2 class="section-title">مراحل اجرای <?= e($service['title']) ?></h2>
        </div>
        <ol class="process-list">
            <?php foreach ($service['steps'] as $i => $step): ?>
            <li>
                <span class="proc-num"><?= e((string)($i + 1)) ?></span>
                <h4><?= e($step['title']) ?></h4>
                <p><?= e($step['desc']) ?></p>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">پرسش‌های متداول</span>
            <h2 class="section-title">سوالات رایج درباره <?= e($service['title']) ?></h2>
        </div>
        <div class="faq-list">
            <?php foreach ($service['faq'] as $item): ?>
            <details class="faq-item">
                <summary><?= e($item['q']) ?></summary>
                <p><?= e($item['a']) ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-inner">
        <div>
            <h2>آماده‌ی شروع پروژه هستید؟</h2>
            <p>برای دریافت مشاوره و قیمت، همین حالا با ما تماس بگیرید.</p>
        </div>
        <div class="cta-actions">
            <a href="/contact" class="btn btn-primary btn-lg">تماس با ما</a>
        </div>
    </div>
</section>
