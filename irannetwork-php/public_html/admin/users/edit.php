<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
if (!Auth::can('manage_users')) { http_response_code(403); exit('دسترسی غیرمجاز.'); }

$m = new User();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$isEdit = $id > 0;
$row = ['name'=>'','email'=>'','role'=>'editor','status'=>'active'];
if ($isEdit) {
    $found = $m->find($id);
    if (!$found) { flash('error','کاربر یافت نشد.'); redirect('/admin/users/'); }
    $row = $found;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $name     = trim((string)($_POST['name'] ?? ''));
    $email    = strtolower(trim((string)($_POST['email'] ?? '')));
    $role     = (string)($_POST['role'] ?? 'editor');
    $status   = (string)($_POST['status'] ?? 'active');
    $password = (string)($_POST['password'] ?? '');

    if (mb_strlen($name) < 2) $errors[] = 'نام باید حداقل ۲ کاراکتر باشد.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'ایمیل معتبر نیست.';
    if (!in_array($role, ['super_admin','admin','editor','user'], true)) $role = 'editor';
    if (!in_array($status, ['active','inactive'], true)) $status = 'active';

    // Only super_admin can create/edit other super_admins.
    if ($role === 'super_admin' && !Auth::isSuperAdmin()) {
        $errors[] = 'فقط سوپر ادمین می‌تواند سوپر ادمین تعیین کند.';
    }
    // A super_admin cannot be demoted by non-super_admin
    if ($isEdit && $row['role'] === 'super_admin' && $role !== 'super_admin' && !Auth::isSuperAdmin()) {
        $errors[] = 'تنزل نقش سوپر ادمین مجاز نیست.';
    }
    // Cannot remove last super_admin
    if ($isEdit && $row['role'] === 'super_admin' && $role !== 'super_admin') {
        $count = (int)Database::fetchColumn('SELECT COUNT(*) FROM users WHERE role = ?', ['super_admin']);
        if ($count <= 1) $errors[] = 'حداقل یک سوپر ادمین باید باقی بماند.';
    }
    // Email uniqueness
    $dup = Database::fetch('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
    if ($dup && (int)$dup['id'] !== $id) $errors[] = 'این ایمیل قبلاً ثبت شده است.';

    if (!$isEdit && strlen($password) < 8) $errors[] = 'رمز عبور باید حداقل ۸ کاراکتر باشد.';
    if ($isEdit && $password !== '' && strlen($password) < 8) $errors[] = 'رمز جدید باید حداقل ۸ کاراکتر باشد.';

    if (!$errors) {
        $data = ['name'=>$name,'email'=>$email,'role'=>$role,'status'=>$status];
        if ($password !== '') $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        if ($isEdit) {
            $m->update($id, $data);
            flash('success','کاربر به‌روزرسانی شد.');
        } else {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
            $id = $m->create($data);
            flash('success','کاربر ساخته شد.');
        }
        redirect('/admin/users/edit.php?id=' . $id);
    }
    $row = array_merge($row, ['name'=>$name,'email'=>$email,'role'=>$role,'status'=>$status]);
}

$pageTitle = $isEdit ? 'ویرایش کاربر' : 'کاربر جدید';
ob_start(); ?>
<form method="post" class="card">
  <?= Csrf::field() ?>
  <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
  <?php foreach ($errors as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>

  <div class="form-grid">
    <div class="form-group"><label>نام *</label>
      <input name="name" required maxlength="150" value="<?= e($row['name']) ?>"></div>
    <div class="form-group"><label>ایمیل *</label>
      <input name="email" type="email" required dir="ltr" maxlength="190" value="<?= e($row['email']) ?>"></div>
    <div class="form-group"><label>نقش</label>
      <select name="role">
        <?php if (Auth::isSuperAdmin()): ?>
          <option value="super_admin" <?= $row['role']==='super_admin'?'selected':'' ?>>سوپر ادمین</option>
        <?php endif; ?>
        <option value="admin"  <?= $row['role']==='admin'?'selected':'' ?>>ادمین</option>
        <option value="editor" <?= $row['role']==='editor'?'selected':'' ?>>ویرایشگر</option>
        <option value="user"   <?= $row['role']==='user'?'selected':'' ?>>کاربر</option>
      </select>
    </div>
    <div class="form-group"><label>وضعیت</label>
      <select name="status">
        <option value="active"   <?= $row['status']==='active'?'selected':'' ?>>فعال</option>
        <option value="inactive" <?= $row['status']==='inactive'?'selected':'' ?>>غیرفعال</option>
      </select>
    </div>
    <div class="form-group"><label><?= $isEdit ? 'رمز جدید (اختیاری)' : 'رمز عبور *' ?></label>
      <input name="password" type="password" minlength="8" <?= $isEdit?'':'required' ?> autocomplete="new-password">
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="/admin/users/">انصراف</a>
    <button class="btn btn-primary"><?= $isEdit ? 'به‌روزرسانی' : 'ذخیره' ?></button>
  </div>

  <p style="margin-top:12px;color:var(--muted);font-size:13px">
    نقش‌ها: <b>سوپر ادمین</b>: دسترسی کامل. <b>ادمین</b>: محتوا + تنظیمات. <b>ویرایشگر</b>: فقط محتوا.
  </p>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../_layout.php';
