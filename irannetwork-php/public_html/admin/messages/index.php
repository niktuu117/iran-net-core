<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new ContactMessage();
$status = (string)($_GET['status'] ?? '');
$where = [];$params=[];
if (in_array($status,['new','read','archived'],true)) { $where[]='status = ?'; $params[]=$status; }
$page = max(1,(int)($_GET['page'] ?? 1));
$result = $m->paginate($page, 30, $where?implode(' AND ',$where):'', $params);

$pageTitle='پیام‌های تماس';
ob_start(); ?>
<div class="card">
  <div class="card-head">
    <h2>پیام‌های تماس (<?= (int)$result['total'] ?>)</h2>
  </div>
  <form class="filters" method="get">
    <select name="status">
      <option value="">همه</option>
      <option value="new"      <?= $status==='new'?'selected':'' ?>>جدید</option>
      <option value="read"     <?= $status==='read'?'selected':'' ?>>خوانده‌شده</option>
      <option value="archived" <?= $status==='archived'?'selected':'' ?>>بایگانی</option>
    </select>
    <button class="btn btn-secondary btn-sm">فیلتر</button>
  </form>
  <table class="table">
    <thead><tr><th>نام</th><th>تلفن</th><th>سرویس</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr></thead>
    <tbody>
    <?php if(!$result['data']):?><tr><td colspan="6" style="text-align:center;color:var(--muted)">پیامی نیست.</td></tr><?php endif; ?>
    <?php foreach($result['data'] as $r): ?>
      <tr>
        <td><a href="/admin/messages/view.php?id=<?= (int)$r['id'] ?>"><?= e($r['name']) ?></a></td>
        <td dir="ltr"><?= e($r['phone']) ?></td>
        <td><?= e($r['service'] ?? '-') ?></td>
        <td><span class="pill pill-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
        <td><?= e(format_date_fa($r['created_at'])) ?></td>
        <td class="row-actions">
          <a class="btn btn-secondary btn-sm" href="/admin/messages/view.php?id=<?= (int)$r['id'] ?>">مشاهده</a>
          <form method="post" action="/admin/messages/delete.php" data-confirm="حذف شود؟" style="display:inline">
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
