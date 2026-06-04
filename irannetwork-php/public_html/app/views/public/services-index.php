<section class="page-header">
  <div class="container">
    <nav class="breadcrumbs"><a href="/">خانه</a> <span>/</span> <span>خدمات</span></nav>
    <h1>خدمات ایران نتورک</h1>
    <p>پشتیبانی، نصب، امنیت، ویپ، سرور و دیجیتال مارکتینگ — همه در یک مجموعه‌ی متخصص.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$services): ?>
      <p class="text-muted">هنوز سرویسی منتشر نشده است.</p>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($services as $s): ?>
          <article class="card-service">
            <?php if (!empty($s['featured_image'])): ?>
              <img src="<?= e($s['featured_image']) ?>" alt="<?= e($s['featured_image_alt'] ?? $s['title']) ?>" loading="lazy">
            <?php endif; ?>
            <h3><a href="/services/<?= e($s['slug']) ?>"><?= e($s['title']) ?></a></h3>
            <p><?= e(excerpt($s['excerpt'] ?? '', 22)) ?></p>
            <a href="/services/<?= e($s['slug']) ?>" class="link-arrow">جزئیات بیشتر</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
