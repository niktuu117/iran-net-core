<section class="page-header">
  <div class="container">
    <nav class="breadcrumbs" aria-label="مسیر">
      <a href="/">خانه</a> <span>/</span> <span>مقالات</span>
    </nav>
    <h1>مقالات و آموزش‌ها</h1>
    <p>تجربه و دانش تیم ایران نتورک در حوزه‌های شبکه، سرور، امنیت و دیجیتال مارکتینگ.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!empty($featured)): ?>
      <h2 class="section-title">مقالات ویژه</h2>
      <div class="grid grid-3">
        <?php foreach ($featured as $p): ?>
          <article class="card-post">
            <?php if (!empty($p['featured_image'])): ?>
              <img src="<?= e($p['featured_image']) ?>" alt="<?= e($p['featured_image_alt'] ?? $p['title']) ?>" loading="lazy">
            <?php endif; ?>
            <h3><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
            <p><?= e(excerpt($p['excerpt'] ?? '', 22)) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <h2 class="section-title" style="margin-top:48px">جدیدترین مقالات</h2>
    <?php if (!$paged['data']): ?>
      <p class="text-muted">هنوز مقاله‌ای منتشر نشده است.</p>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($paged['data'] as $p): ?>
          <article class="card-post">
            <?php if (!empty($p['featured_image'])): ?>
              <img src="<?= e($p['featured_image']) ?>" alt="<?= e($p['featured_image_alt'] ?? $p['title']) ?>" loading="lazy">
            <?php endif; ?>
            <h3><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
            <p><?= e(excerpt($p['excerpt'] ?? $p['content'] ?? '', 24)) ?></p>
            <a href="/blog/<?= e($p['slug']) ?>" class="link-arrow">ادامه مطلب</a>
          </article>
        <?php endforeach; ?>
      </div>
      <?php if ($paged['pages'] > 1): ?>
        <nav class="pagination" aria-label="صفحه‌بندی">
          <?php for ($i=1; $i<=$paged['pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i===$paged['page']?'active':'' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($categories)): ?>
      <h2 class="section-title" style="margin-top:48px">دسته‌بندی‌ها</h2>
      <div class="chip-row">
        <?php foreach ($categories as $c): ?>
          <a href="/category/<?= e($c['slug']) ?>" class="chip"><?= e($c['name']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
