<section class="page-header">
  <div class="container">
    <nav class="breadcrumbs" aria-label="مسیر">
      <a href="/">خانه</a> <span>/</span>
      <a href="/blog">مقالات</a> <span>/</span>
      <?php if (!empty($category)): ?>
        <a href="/category/<?= e($category['slug']) ?>"><?= e($category['name']) ?></a> <span>/</span>
      <?php endif; ?>
      <span><?= e($post['title']) ?></span>
    </nav>
    <h1><?= e($post['title']) ?></h1>
    <div class="post-meta">
      <?php if (!empty($author['name'])): ?><span>نویسنده: <?= e($author['name']) ?></span><?php endif; ?>
      <?php if (!empty($post['published_at'])): ?><span>· <?= e(format_date_fa($post['published_at'])) ?></span><?php endif; ?>
    </div>
  </div>
</section>

<article class="section">
  <div class="container article-grid">
    <div class="article-main">
      <?php if (!empty($post['featured_image'])): ?>
        <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['featured_image_alt'] ?? $post['title']) ?>" class="article-hero" loading="eager">
      <?php endif; ?>
      <div class="article-content">
        <?= $post['content'] ?>
      </div>

      <?php if (!empty($tags)): ?>
        <div class="chip-row" style="margin-top:24px">
          <?php foreach ($tags as $t): ?>
            <a class="chip" href="/tag/<?= e($t['slug']) ?>">#<?= e($t['name']) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

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
    </div>

    <aside class="article-aside">
      <?php if (!empty($services)): ?>
        <div class="aside-block">
          <h3>سرویس‌های مرتبط</h3>
          <ul>
            <?php foreach ($services as $s): ?>
              <li><a href="/services/<?= e($s['slug']) ?>"><?= e($s['title']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (!empty($related)): ?>
        <div class="aside-block">
          <h3>مقالات مرتبط</h3>
          <ul>
            <?php foreach ($related as $r): ?>
              <li><a href="/blog/<?= e($r['slug']) ?>"><?= e($r['title']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</article>
