<?php
require_once __DIR__ . '/_bootstrap.php';
Auth::requireAdmin();

$postCount    = (new Post())->count();
$serviceCount = (new Service())->count();
$pageCount    = (new Page())->count();
$mediaCount   = (new Media())->count();
$msgNew       = (new ContactMessage())->count("status='new'");
$msgTotal     = (new ContactMessage())->count();

$latestMessages = Database::fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$latestPosts    = Database::fetchAll("SELECT id,title,status,created_at FROM posts ORDER BY created_at DESC LIMIT 5");

$pageTitle = 'داشبورد';
ob_start(); ?>

<div class="stat-grid">
  <div class="stat"><div class="stat-icon"><?= icon_svg('doc',22) ?></div><div><div class="stat-val"><?= (int)$postCount ?></div><div class="stat-label">مقاله‌ها</div></div></div>
  <div class="stat"><div class="stat-icon"><?= icon_svg('server',22) ?></div><div><div class="stat-val"><?= (int)$serviceCount ?></div><div class="stat-label">سرویس‌ها</div></div></div>
  <div class="stat"><div class="stat-icon"><?= icon_svg('folder',22) ?></div><div><div class="stat-val"><?= (int)$pageCount ?></div><div class="stat-label">صفحات</div></div></div>
  <div class="stat"><div class="stat-icon"><?= icon_svg('image',22) ?></div><div><div class="stat-val"><?= (int)$mediaCount ?></div><div class="stat-label">رسانه‌ها</div></div></div>
  <div class="stat"><div class="stat-icon"><?= icon_svg('inbox',22) ?></div><div><div class="stat-val"><?= (int)$msgNew ?> / <?= (int)$msgTotal ?></div><div class="stat-label">پیام جدید / کل</div></div></div>
</div>

<div class="card">
  <div class="card-head"><h2>آخرین پیام‌های تماس</h2><a class="btn btn-secondary btn-sm" href="/admin/messages/">مشاهده همه</a></div>
  <table class="table">
    <thead><tr><th>نام</th><th>تلفن</th><th>سرویس</th><th>وضعیت</th><th>تاریخ</th></tr></thead>
    <tbody>
      <?php if (!$latestMessages): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--muted)">پیامی ثبت نشده.</td></tr>
      <?php endif; foreach ($latestMessages as $m): ?>
      <tr>
        <td><?= e($m['name']) ?></td>
        <td dir="ltr"><?= e($m['phone']) ?></td>
        <td><?= e($m['service'] ?? '-') ?></td>
        <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
        <td><?= e(format_date_fa($m['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-head"><h2>آخرین مقاله‌ها</h2><a class="btn btn-secondary btn-sm" href="/admin/posts/">مشاهده همه</a></div>
  <table class="table">
    <thead><tr><th>عنوان</th><th>وضعیت</th><th>تاریخ</th></tr></thead>
    <tbody>
      <?php if (!$latestPosts): ?>
        <tr><td colspan="3" style="text-align:center;color:var(--muted)">مقاله‌ای ثبت نشده.</td></tr>
      <?php endif; foreach ($latestPosts as $p): ?>
      <tr>
        <td><a href="/admin/posts/edit.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a></td>
        <td><span class="pill pill-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
        <td><?= e(format_date_fa($p['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/_layout.php';
