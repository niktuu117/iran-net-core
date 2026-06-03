<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new Tag();
$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId ? $m->find($editId) : null;
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    Csrf::check();
    $name = trim((string)($_POST['name'] ?? ''));
    $slug = trim((string)($_POST['slug'] ?? ''));
    $id   = (int)($_POST['id'] ?? 0);
    if (mb_strlen($name)<2) $errors[]='نام الزامی است.';
    $slug = unique_slug(slugify($slug ?: $name), 'tags', $id ?: null);
    if (!$errors) {
        $data=['name'=>$name,'slug'=>$slug];
        if ($id>0) $m->update($id,$data); else $m->create($data);
        flash('success', $id>0?'به‌روزرسانی شد.':'برچسب ساخته شد.');
        redirect('/admin/tags/');
    }
}
$rows = $m->all('name ASC');
$pageTitle='برچسب‌ها';
ob_start(); ?>
<div class="form-grid" style="grid-template-columns:1fr 1fr">
  <form method="post" class="card">
    <?= Csrf::field() ?>
    <?php if($editing):?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
    <h2><?= $editing?'ویرایش برچسب':'برچسب جدید' ?></h2>
    <?php foreach($errors as $err):?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
    <div class="form-group" style="margin-bottom:12px"><label>نام *</label><input id="name" name="name" required value="<?= e($editing['name'] ?? '') ?>"></div>
    <div class="form-group" style="margin-bottom:12px"><label>اسلاگ</label><input name="slug" dir="ltr" value="<?= e($editing['slug'] ?? '') ?>" data-slug-from="name"></div>
    <div style="display:flex;gap:8px"><?php if($editing):?><a class="btn btn-ghost" href="/admin/tags/">انصراف</a><?php endif; ?>
      <button class="btn btn-primary"><?= $editing?'به‌روزرسانی':'ذخیره' ?></button></div>
  </form>
  <div class="card">
    <h2>لیست برچسب‌ها (<?= count($rows) ?>)</h2>
    <table class="table">
      <thead><tr><th>نام</th><th>اسلاگ</th><th>عملیات</th></tr></thead>
      <tbody>
      <?php if(!$rows):?><tr><td colspan="3" style="text-align:center;color:var(--muted)">موردی نیست.</td></tr><?php endif; ?>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= e($r['name']) ?></td><td dir="ltr"><?= e($r['slug']) ?></td>
          <td class="row-actions">
            <a class="btn btn-secondary btn-sm" href="?edit=<?= (int)$r['id'] ?>"><?= icon_svg('edit',14) ?></a>
            <form method="post" action="/admin/tags/delete.php" data-confirm="حذف شود؟" style="display:inline">
              <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-danger btn-sm"><?= icon_svg('trash',14) ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
