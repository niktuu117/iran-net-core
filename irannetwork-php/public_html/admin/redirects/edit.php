<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new Redirect();
$isEdit = isset($_GET['id']) || (int)($_POST['id'] ?? 0) > 0;
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$row = ['old_url'=>'','new_url'=>'','status_code'=>301,'is_active'=>1];
if ($isEdit) {
    $found = $m->find($id);
    if (!$found) { flash('error','ریدایرکت یافت نشد.'); redirect('/admin/redirects/'); }
    $row = $found;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    Csrf::check();
    $data = [
        'old_url'    => '/' . ltrim(trim((string)($_POST['old_url'] ?? '')), '/'),
        'new_url'    => trim((string)($_POST['new_url'] ?? '')),
        'status_code'=> in_array((int)($_POST['status_code'] ?? 301), [301,302,307,308], true) ? (int)$_POST['status_code'] : 301,
        'is_active'  => !empty($_POST['is_active']) ? 1 : 0,
    ];
    if ($data['old_url'] === '/' || $data['old_url'] === '') $errors[] = 'آدرس قدیمی الزامی است.';
    if ($data['new_url'] === '') $errors[] = 'آدرس جدید الزامی است.';
    if ($data['old_url'] === $data['new_url']) $errors[] = 'آدرس مبدا و مقصد یکسان است.';
    if (!$errors) {
        try {
            if ($isEdit) $m->update($id, $data); else $id = $m->create($data);
            flash('success', $isEdit?'ریدایرکت به‌روزرسانی شد.':'ریدایرکت اضافه شد.');
            redirect('/admin/redirects/');
        } catch (Throwable $e) {
            $errors[] = 'ذخیره ناموفق بود (احتمالاً آدرس قدیمی تکراری است).';
        }
    }
    $row = array_merge($row, $data);
}
$pageTitle = $isEdit ? 'ویرایش ریدایرکت' : 'ریدایرکت جدید';
ob_start(); ?>
<form method="post" class="card">
  <?= Csrf::field() ?><?php if($isEdit):?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
  <?php foreach($errors as $e):?><div class="flash flash-error"><?= e($e) ?></div><?php endforeach; ?>
  <div class="form-grid">
    <div class="form-group full">
      <label>آدرس قدیمی (شروع با /)</label>
      <input name="old_url" dir="ltr" required maxlength="500" value="<?= e($row['old_url']) ?>" placeholder="/old-page">
    </div>
    <div class="form-group full">
      <label>آدرس جدید (مسیر داخلی یا URL کامل)</label>
      <input name="new_url" dir="ltr" required maxlength="500" value="<?= e($row['new_url']) ?>" placeholder="/new-page یا https://...">
    </div>
    <div class="form-group">
      <label>کد وضعیت</label>
      <select name="status_code">
        <?php foreach ([301=>'301 (دائمی)',302=>'302 (موقت)',307=>'307',308=>'308'] as $k=>$lab): ?>
          <option value="<?= $k ?>" <?= (int)$row['status_code']===$k?'selected':'' ?>><?= $lab ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label><input type="checkbox" name="is_active" value="1" <?= (int)$row['is_active']?'checked':'' ?>> فعال</label>
    </div>
  </div>
  <div class="form-actions">
    <a class="btn btn-ghost" href="/admin/redirects/">انصراف</a>
    <button class="btn btn-primary"><?= $isEdit?'به‌روزرسانی':'ذخیره' ?></button>
  </div>
</form>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
