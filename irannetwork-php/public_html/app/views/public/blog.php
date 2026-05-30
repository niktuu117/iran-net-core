<?php
$posts = [
    ['title' => 'راهنمای انتخاب فایروال سازمانی', 'excerpt' => 'مقایسه‌ی برندهای محبوب فایروال برای کسب‌وکارهای کوچک و متوسط.', 'tag' => 'امنیت'],
    ['title' => 'چرا VoIP برای شرکت‌ها سودآور است؟', 'excerpt' => 'مزایای اقتصادی و عملیاتی استفاده از سیستم‌های ویپ سازمانی.', 'tag' => 'ویپ'],
    ['title' => 'استانداردهای کابل‌کشی شبکه', 'excerpt' => 'مرور سریع TIA/EIA-568 و نکات اجرای استاندارد پسیو.', 'tag' => 'پسیو'],
    ['title' => 'پشتیبان‌گیری امن از سرور', 'excerpt' => 'استراتژی ۳-۲-۱ و ابزارهای حرفه‌ای بکاپ سرور.', 'tag' => 'سرور'],
    ['title' => 'سئو فنی برای سایت‌های شرکتی', 'excerpt' => 'نکات کلیدی Core Web Vitals و ساختار قابل ایندکس.', 'tag' => 'دیجیتال مارکتینگ'],
    ['title' => 'مقایسه سوئیچ‌های لایه ۲ و ۳', 'excerpt' => 'کدام سوئیچ برای شبکه‌ی شما مناسب‌تر است؟', 'tag' => 'اکتیو'],
];
?>
<section class="page-header">
    <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر"><a href="/">خانه</a> <span>/</span> <span>مقالات</span></nav>
        <h1>مقالات و آموزش‌ها</h1>
        <p>مطالب تخصصی درباره شبکه، سرور، امنیت، ویپ و دیجیتال مارکتینگ.</p>
        <p class="note-inline">این بخش در فاز ۳ به CMS داینامیک متصل می‌شود.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="blog-grid">
            <?php foreach ($posts as $p): ?>
            <article class="blog-card">
                <div class="blog-thumb" aria-hidden="true">
                    <span class="blog-tag"><?= e($p['tag']) ?></span>
                </div>
                <div class="blog-body">
                    <h3><?= e($p['title']) ?></h3>
                    <p><?= e($p['excerpt']) ?></p>
                    <span class="link-arrow">به‌زودی <?= icon_svg('arrow', 16) ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
