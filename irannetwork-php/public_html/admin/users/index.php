<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
if (!Auth::can('manage_users')) { http_response_code(403); exit('دسترسی غیرمجاز.'); }

$m = new User();
$q       = trim((string)($_GET['q'] ?? ''));
$role    = trim((string)($_GET['role'] ?? ''));
$where   = '1=1';
$params  = [];
if ($q !== '') {
    $where .= ' AND (name LIKE ? OR email LIKE ?)';
    $params[] = "%{$q}%"; $params[] = "%{$q}%";
}
if (in_array($role, ['super_admin','admin','editor','user'], true)) {
    $where .= ' AND role = ?'; $params[] = $role;
}
$page = max(1, (int)($_GET['page'] ?? 1));
$rows = $m->paginate($page, 25, $where, $params, 'id ASC');

$pageTitle = 'مدیریت کاربران';
ob_start(); ?>
<div class="card">
  <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
    <h2>کاربران (<?= (int)$rows['total'] ?>)</h2>
    <a class="btn btn-primary" href="/admin/users/edit.php"><?= icon_svg('plus',14) ?> کاربر جدید</a>
  </div>

  <form method="get" class="form-grid" style="grid-template-columns:2fr 1fr auto;gap:12px;margin:12px 0">
    <div class="form-group"><input name="q" placeholder="جستجو نام/ایمیل…" value="<?= e($q) ?>"></div>
    <div class="form-group">
      <select name="role">
        <option value="">همه نقش‌ها</option>
        <option value="super_admin" <?= $role==='super_admin'?'selected':'' ?>>سوپر ادمین</option>
        <option value="admin"       <?= $role==='admin'?'selected':'' ?>>ادمین</option>
        <option value="editor"      <?= $role==='editor'?'selected':'' ?>>ویرایشگر</option>
        <option value="user"        <?= $role==='user'?'selected':'' ?>>کاربر</option>
      </select>
    </div>
    <button class="btn btn-secondary">اعمال</button>
  </form>

  <table class="data-table">
    <thead><tr><th>#</th><th>نام</th><th>ایمیل</th><th>نقش</th><th>وضعیت</th><th>تاریخ</th><th></th></tr></thead>
    <tbody>
      <?php if (!$rows['data']): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--muted)">کاربری یافت نشد.</td></tr>
      <?php endif; ?>
      <?php $meId = Auth::id(); foreach ($rows['data'] as $u):
        $roleLabels = ['super_admin'=>'سوپر ادمین','admin'=>'ادمین','editor'=>'ویرایشگر','user'=>'کاربر'];
        $isSuper = $u['role']==='super_admin';
      ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= e($u['name']) ?><?= $u['id']==$meId ? ' <small style="color:var(--muted)">(شما)</small>' : '' ?></td>
          <td dir="ltr"><?= e($u['email']) ?></td>
          <td><span class="badge badge-<?= e($u['role']) ?>"><?= e($roleLabels[$u['role']] ?? $u['role']) ?></span></td>
          <td><?= $u['status']==='active' ? '✅ فعال' : '⛔ غیرفعال' ?></td>
          <td><?= format_date_fa($u['created_at']) ?></td>
          <td>
            <a class="btn btn-secondary btn-sm" href="/admin/users/edit.php?id=<?= (int)$u['id'] ?>">ویرایش</a>
            <?php if (!$isSuper && $u['id']!=$meId): ?>
              <form method="post" action="/admin/users/delete.php" data-confirm="حذف کاربر «<?= e($u['name']) ?>»؟" style="display:inline">
                <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                <button class="btn btn-danger btn-sm">حذف</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../_layout.php';
