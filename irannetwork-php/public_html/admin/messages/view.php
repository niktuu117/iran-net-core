<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new ContactMessage();
$id = (int)($_GET['id'] ?? 0);
$row = $m->find($id);
if (!$row) { flash('error','پیام یافت نشد.'); redirect('/admin/messages/'); }

if ($_SERVER['REQUEST_METHOD']==='POST') {
    Csrf::check();
    $action = (string)($_POST['action'] ?? '');
    if (in_array($action, ['read','archived','new'], true)) {
        $m->update($id, ['status'=>$action]);
        flash('success','وضعیت به‌روزرسانی شد.');
    }
    redirect('/admin/messages/view.php?id='.$id);
}

// Auto mark-as-read on first view
if ($row['status'] === 'new') {
    $m->update($id, ['status'=>'read']);
    $row['status'] = 'read';
}

$pageTitle='مشاهده پیام';
ob_start(); ?>
<div class="card">
  <div class="card-head">
    <h2><?= e($row['name']) ?> — <small style="color:var(--muted)"><?= e(format_date_fa($row['created_at'])) ?></small></h2>
    <a class="btn btn-secondary btn-sm" href="/admin/messages/">بازگشت</a>
  </div>
  <table class="table" style="margin-bottom:16px">
    <tbody>
      <tr><th style="width:140px">تلفن</th><td dir="ltr"><a href="tel:<?= e($row['phone']) ?>"><?= e($row['phone']) ?></a></td></tr>
      <tr><th>ایمیل</th><td dir="ltr"><?= $row['email'] ? '<a href="mailto:'.e($row['email']).'">'.e($row['email']).'</a>' : '-' ?></td></tr>
      <tr><th>سرویس</th><td><?= e($row['service'] ?? '-') ?></td></tr>
      <tr><th>وضعیت</th><td><span class="pill pill-<?= e($row['status']) ?>"><?= e($row['status']) ?></span></td></tr>
    </tbody>
  </table>
  <h3>متن پیام</h3>
  <div style="background:#0a1330;border:1px solid var(--border);padding:14px;border-radius:8px;white-space:pre-wrap;line-height:2"><?= e($row['message']) ?></div>

  <div class="form-actions">
    <form method="post" style="display:inline"><?= Csrf::field() ?><input type="hidden" name="action" value="archived"><button class="btn btn-secondary">بایگانی</button></form>
    <form method="post" style="display:inline"><?= Csrf::field() ?><input type="hidden" name="action" value="new"><button class="btn btn-ghost">علامت‌گذاری به جدید</button></form>
    <form method="post" action="/admin/messages/delete.php" data-confirm="پیام حذف شود؟" style="display:inline">
      <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
      <button class="btn btn-danger">حذف</button>
    </form>
  </div>
</div>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
