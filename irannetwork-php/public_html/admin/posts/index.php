<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();

$q      = trim((string)($_GET['q'] ?? ''));
$status = (string)($_GET['status'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));

$where = [];
$params = [];
if ($q !== '')      { $where[] = '(title LIKE ? OR slug LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($status !== '') { $where[] = 'status = ?'; $params[] = $status; }
$whereSql = $where ? implode(' AND ', $where) : '';

$result = (new Post())->paginate($page, 20, $whereSql, $params, 'id DESC');

$pageTitle = 'مقاله‌ها';
ob_start(); ?>
<div class="card">
  <div class="card-head">
    <h2>لیست مقاله‌ها (<?= (int)$result['total'] ?>)</h2>
    <a class="btn btn-primary" href="/admin/posts/create.php"><?= icon_svg('plus',16) ?> مقاله جدید</a>
  </div>
  <form class="filters" method="get">
    <input type="text" name="q" placeholder="جستجو…" value="<?= e($q) ?>">
    <select name="status">
      <option value="">همه وضعیت‌ها</option>
      <option value="draft" <?= $status==='draft'?'selected':'' ?>>پیش‌نویس</option>
      <option value="published" <?= $status==='published'?'selected':'' ?>>منتشر شده</option>
      <option value="scheduled" <?= $status==='scheduled'?'selected':'' ?>>زمان‌بندی شده</option>
    </select>
    <button class="btn btn-secondary btn-sm">فیلتر</button>
  </form>
  <table class="table">
    <thead><tr><th>عنوان</th><th>اسلاگ</th><th>وضعیت</th><th>ویژه</th><th>تاریخ</th><th>عملیات</th></tr></thead>
    <tbody>
      <?php if (!$result['data']): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--muted)">موردی یافت نشد.</td></tr>
      <?php endif; foreach ($result['data'] as $p): ?>
      <tr>
        <td><a href="/admin/posts/edit.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a></td>
        <td dir="ltr"><?= e($p['slug']) ?></td>
        <td><span class="pill pill-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
        <td><?= $p['featured'] ? '★' : '-' ?></td>
        <td><?= e(format_date_fa($p['created_at'])) ?></td>
        <td class="row-actions">
          <a class="btn btn-secondary btn-sm" href="/admin/posts/edit.php?id=<?= (int)$p['id'] ?>"><?= icon_svg('edit',14) ?></a>
          <form method="post" action="/admin/posts/delete.php" data-confirm="مقاله حذف شود؟" style="display:inline">
            <?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button class="btn btn-danger btn-sm"><?= icon_svg('trash',14) ?></button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../_layout.php';
