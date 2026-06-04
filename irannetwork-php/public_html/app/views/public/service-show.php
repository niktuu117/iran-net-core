<section class="page-header">
  <div class="container">
    <nav class="breadcrumbs">
      <a href="/">خانه</a> <span>/</span> <a href="/services">خدمات</a> <span>/</span>
      <span><?= e($service['title']) ?></span>
    </nav>
    <h1><?= e($service['h1'] ?? $service['title']) ?></h1>
    <?php if (!empty($service['excerpt'])): ?><p><?= e($service['excerpt']) ?></p><?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container article-grid">
    <div class="article-main">
      <?php if (!empty($service['featured_image'])): ?>
        <img src="<?= e($service['featured_image']) ?>" alt="<?= e($service['featured_image_alt'] ?? $service['title']) ?>" class="article-hero" loading="eager">
      <?php endif; ?>
      <div class="article-content"><?= $service['content'] ?></div>

      <?php if (!empty($faqs)): ?>
        <section class="section-faq">
          <h2>سوالات متداول</h2>
          <?php foreach ($faqs as $f): ?>
            <details>
              <summary><?= e($f['question']) ?></summary>
              <div><?= nl2br(e($f['answer'])) ?></div>
            </details>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>

      <div class="cta-block">
        <h3>برای این سرویس مشاوره می‌خواهید؟</h3>
        <a href="/contact" class="btn btn-primary">درخواست مشاوره</a>
      </div>
    </div>

    <aside class="article-aside">
      <?php if (!empty($relatedPosts)): ?>
        <div class="aside-block">
          <h3>مقالات مرتبط</h3>
          <ul>
            <?php foreach ($relatedPosts as $r): ?>
              <li><a href="/blog/<?= e($r['slug']) ?>"><?= e($r['title']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <?php if (!empty($otherServices)): ?>
        <div class="aside-block">
          <h3>سایر خدمات</h3>
          <ul>
            <?php foreach ($otherServices as $s): ?>
              <li><a href="/services/<?= e($s['slug']) ?>"><?= e($s['title']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</section>
