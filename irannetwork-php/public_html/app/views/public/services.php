<section class="page-header">
    <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
            <a href="/">خانه</a> <span>/</span> <span>خدمات</span>
        </nav>
        <h1>خدمات ایران نتورک</h1>
        <p>پوشش کامل نیازهای شبکه، سرور، امنیت و ارتباطات برای کسب‌وکارهای حرفه‌ای.</p>
    </div>
</section>

<section class="section">
    <div class="container">
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

<section class="cta-band">
    <div class="container cta-inner">
        <div>
            <h2>نمی‌دانید کدام سرویس مناسب شماست؟</h2>
            <p>کارشناسان ما رایگان به شما مشاوره می‌دهند.</p>
        </div>
        <div class="cta-actions">
            <a href="/contact" class="btn btn-primary btn-lg">دریافت مشاوره</a>
        </div>
    </div>
</section>
