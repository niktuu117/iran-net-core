<section class="page-header">
  <div class="container">
    <nav class="breadcrumbs"><a href="/">خانه</a> <span>/</span> <span><?= e($page['title']) ?></span></nav>
    <h1><?= e($page['h1'] ?? $page['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="article-content"><?= $page['content'] ?></div>

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
</section>
