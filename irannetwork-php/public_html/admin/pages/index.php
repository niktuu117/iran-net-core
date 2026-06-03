<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$rows = (new Page())->all('id ASC');
$pageTitle='صفحات';
ob_start(); ?>
<div class="card">
  <div class="card-head"><h2>صفحات (<?= count($rows) ?>)</h2>
    <a class="btn btn-primary" href="/admin/pages/create.php"><?= icon_svg('plus',16) ?> صفحه جدید</a></div>
  <table class="table">
    <thead><tr><th>#</th><th>عنوان</th><th>اسلاگ</th><th>وضعیت</th><th>عملیات</th></tr></thead>
    <tbody>
    <?php if(!$rows):?><tr><td colspan="5" style="text-align:center;color:var(--muted)">موردی نیست.</td></tr><?php endif; ?>
    <?php foreach($rows as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><a href="/admin/pages/edit.php?id=<?= (int)$r['id'] ?>"><?= e($r['title']) ?></a></td>
        <td dir="ltr"><?= e($r['slug']) ?></td>
        <td><span class="pill pill-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
        <td class="row-actions">
          <a class="btn btn-secondary btn-sm" href="/admin/pages/edit.php?id=<?= (int)$r['id'] ?>"><?= icon_svg('edit',14) ?></a>
          <form method="post" action="/admin/pages/delete.php" data-confirm="صفحه حذف شود؟" style="display:inline">
            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-danger btn-sm"><?= icon_svg('trash',14) ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
