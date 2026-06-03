<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new Media();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$row = $m->find($id);
if (!$row) { flash('error','فایل یافت نشد.'); redirect('/admin/media/'); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    Csrf::check();
    $m->update($id, [
        'title'=>trim((string)($_POST['title']??'')) ?: null,
        'alt'  =>trim((string)($_POST['alt']??''))   ?: null,
        'caption'=>trim((string)($_POST['caption']??'')) ?: null,
    ]);
    flash('success','به‌روزرسانی شد.');
    redirect('/admin/media/edit.php?id='.$id);
}
$pageTitle='ویرایش رسانه';
ob_start(); ?>
<form method="post" class="card">
  <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
  <div class="form-grid">
    <div class="form-group full">
      <?php if (str_starts_with((string)$row['mime_type'],'image/')): ?>
        <img src="<?= e($row['url']) ?>" alt="" style="max-width:280px;border-radius:8px;border:1px solid var(--border)">
      <?php endif; ?>
      <p>URL: <code dir="ltr"><?= e($row['url']) ?></code>
        <button type="button" class="btn btn-secondary btn-sm" data-copy="<?= e($row['url']) ?>">کپی</button></p>
    </div>
    <div class="form-group"><label>عنوان</label><input name="title" maxlength="255" value="<?= e($row['title']) ?>"></div>
    <div class="form-group"><label>متن جایگزین</label><input name="alt" maxlength="255" value="<?= e($row['alt']) ?>"></div>
    <div class="form-group full"><label>توضیح</label><textarea name="caption" rows="3"><?= e($row['caption']) ?></textarea></div>
  </div>
  <div class="form-actions">
    <a class="btn btn-ghost" href="/admin/media/">انصراف</a>
    <button class="btn btn-primary">به‌روزرسانی</button>
  </div>
</form>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
