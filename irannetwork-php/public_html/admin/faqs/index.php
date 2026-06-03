<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$rows = (new Faq())->all('sort_order ASC, id ASC');
$pageTitle='سوالات متداول';
ob_start(); ?>
<div class="card">
  <div class="card-head"><h2>سوالات متداول (<?= count($rows) ?>)</h2>
    <a class="btn btn-primary" href="/admin/faqs/create.php"><?= icon_svg('plus',16) ?> سوال جدید</a></div>
  <table class="table">
    <thead><tr><th>پرسش</th><th>ترتیب</th><th>فعال</th><th>عملیات</th></tr></thead>
    <tbody>
    <?php if(!$rows):?><tr><td colspan="4" style="text-align:center;color:var(--muted)">موردی نیست.</td></tr><?php endif; ?>
    <?php foreach($rows as $r): ?>
      <tr>
        <td><a href="/admin/faqs/edit.php?id=<?= (int)$r['id'] ?>"><?= e(mb_substr($r['question'],0,90)) ?></a></td>
        <td><?= (int)$r['sort_order'] ?></td>
        <td><?= $r['is_active']?'✓':'-' ?></td>
        <td class="row-actions">
          <a class="btn btn-secondary btn-sm" href="/admin/faqs/edit.php?id=<?= (int)$r['id'] ?>"><?= icon_svg('edit',14) ?></a>
          <form method="post" action="/admin/faqs/delete.php" data-confirm="حذف شود؟" style="display:inline">
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
