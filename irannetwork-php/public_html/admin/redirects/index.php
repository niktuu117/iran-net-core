<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new Redirect();
$rows = $m->paginate(max(1,(int)($_GET['page'] ?? 1)), 30);
$pageTitle = 'مدیریت ریدایرکت‌ها';
ob_start(); ?>
<div class="card">
  <div class="card-head" style="display:flex;justify-content:space-between;align-items:center">
    <h2>ریدایرکت‌ها (<?= (int)$rows['total'] ?>)</h2>
    <a class="btn btn-primary" href="/admin/redirects/edit.php"><?= icon_svg('plus',14) ?> جدید</a>
  </div>
  <table class="data-table">
    <thead><tr><th>#</th><th>آدرس قدیمی</th><th>آدرس جدید</th><th>کد</th><th>فعال</th><th>بازدید</th><th></th></tr></thead>
    <tbody>
      <?php if (!$rows['data']): ?><tr><td colspan="7" style="text-align:center;color:var(--muted)">هیچ ریدایرکتی ثبت نشده.</td></tr><?php endif; ?>
      <?php foreach ($rows['data'] as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td dir="ltr"><?= e($r['old_url']) ?></td>
          <td dir="ltr"><?= e($r['new_url']) ?></td>
          <td><?= (int)$r['status_code'] ?></td>
          <td><?= (int)$r['is_active'] ? '✅' : '⛔' ?></td>
          <td><?= (int)$r['hits'] ?></td>
          <td>
            <a href="/admin/redirects/edit.php?id=<?= (int)$r['id'] ?>">ویرایش</a>
            <form method="post" action="/admin/redirects/delete.php" data-confirm="حذف شود؟" style="display:inline">
              <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-danger btn-sm">حذف</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
