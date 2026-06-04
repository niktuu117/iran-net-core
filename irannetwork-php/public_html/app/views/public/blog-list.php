<section class="page-header">
  <div class="container">
    <nav class="breadcrumbs"><a href="/">خانه</a> <span>/</span> <a href="/blog">مقالات</a> <span>/</span> <span><?= e($heading) ?></span></nav>
    <h1><?= e($heading) ?></h1>
    <?php if (!empty($description)): ?><p><?= e($description) ?></p><?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$paged['data']): ?>
      <p class="text-muted">مقاله‌ای در این بخش یافت نشد.</p>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($paged['data'] as $p): ?>
          <article class="card-post">
            <?php if (!empty($p['featured_image'])): ?>
              <img src="<?= e($p['featured_image']) ?>" alt="<?= e($p['featured_image_alt'] ?? $p['title']) ?>" loading="lazy">
            <?php endif; ?>
            <h3><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
            <p><?= e(excerpt($p['excerpt'] ?? $p['content'] ?? '', 22)) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
      <?php if ($paged['pages'] > 1): ?>
        <nav class="pagination">
          <?php for ($i=1; $i<=$paged['pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i===$paged['page']?'active':'' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
