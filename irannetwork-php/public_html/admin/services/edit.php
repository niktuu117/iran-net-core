<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new Service();
$isEdit = isset($_GET['id']) || (int)($_POST['id'] ?? 0) > 0;
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$row = ['title'=>'','slug'=>'','h1'=>'','excerpt'=>'','content'=>'','status'=>'draft','featured_image'=>'','featured_image_alt'=>'','sort_order'=>0];
if ($isEdit) {
    $found = $m->find($id);
    if (!$found) { flash('error','سرویس یافت نشد.'); redirect('/admin/services/'); }
    $row = $found;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    Csrf::check();
    $data = [
        'title' => trim((string)($_POST['title'] ?? '')),
        'slug'  => trim((string)($_POST['slug'] ?? '')),
        'h1'    => trim((string)($_POST['h1'] ?? '')),
        'excerpt'=> trim((string)($_POST['excerpt'] ?? '')) ?: null,
        'content'=> sanitize_html((string)($_POST['content'] ?? '')),
        'status' => in_array($_POST['status'] ?? '', ['draft','published'], true) ? $_POST['status'] : 'draft',
        'featured_image' => trim((string)($_POST['featured_image'] ?? '')) ?: null,
        'featured_image_alt' => trim((string)($_POST['featured_image_alt'] ?? '')) ?: null,
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
    ];
    if (mb_strlen($data['title'])<2) $errors[]='عنوان الزامی است.';
    if ($data['h1']==='') $data['h1'] = $data['title'];
    if ($data['slug']==='') $data['slug'] = slugify($data['title']);
    $data['slug'] = unique_slug(slugify($data['slug']), 'services', $isEdit?$id:null);
    if (!$errors) {
        if ($isEdit) $m->update($id,$data); else $id = $m->create($data);
        require_once __DIR__.'/../_seo_save.php';
        seo_save_from_post('service', (int)$id);
        flash('success', $isEdit?'سرویس به‌روزرسانی شد.':'سرویس ساخته شد.');
        redirect('/admin/services/edit.php?id='.$id);
    }
    $row = array_merge($row,$data);
}
$pageTitle = $isEdit?'ویرایش سرویس':'سرویس جدید';
ob_start(); ?>
<form method="post" class="card">
  <?= Csrf::field() ?><?php if($isEdit):?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
  <?php foreach($errors as $e):?><div class="flash flash-error"><?= e($e) ?></div><?php endforeach; ?>
  <div class="form-grid">
    <div class="form-group full"><label>عنوان *</label><input id="title" name="title" required maxlength="255" value="<?= e($row['title']) ?>"></div>
    <div class="form-group"><label>اسلاگ</label><input name="slug" dir="ltr" maxlength="190" value="<?= e($row['slug']) ?>" data-slug-from="title"></div>
    <div class="form-group"><label>H1</label><input name="h1" maxlength="255" value="<?= e($row['h1']) ?>"></div>
    <div class="form-group full"><label>خلاصه</label><textarea name="excerpt" rows="3" maxlength="500"><?= e($row['excerpt']) ?></textarea></div>
    <div class="form-group full"><label>محتوا</label><textarea name="content" class="js-editor" rows="12"><?= e($row['content']) ?></textarea></div>
    <div class="form-group"><label>وضعیت</label><select name="status">
      <option value="draft" <?= $row['status']==='draft'?'selected':'' ?>>پیش‌نویس</option>
      <option value="published" <?= $row['status']==='published'?'selected':'' ?>>منتشر شده</option>
    </select></div>
    <div class="form-group"><label>ترتیب نمایش</label><input name="sort_order" type="number" value="<?= (int)$row['sort_order'] ?>"></div>
    <div class="form-group"><label>تصویر شاخص (URL)</label><input name="featured_image" dir="ltr" value="<?= e($row['featured_image']) ?>"></div>
    <div class="form-group"><label>متن جایگزین تصویر</label><input name="featured_image_alt" maxlength="255" value="<?= e($row['featured_image_alt']) ?>"></div>
  </div>
  <div class="form-actions">
    <a class="btn btn-ghost" href="/admin/services/">انصراف</a>
    <button class="btn btn-primary"><?= $isEdit?'به‌روزرسانی':'ذخیره' ?></button>
  </div>
  <?php if ($isEdit): $entityType='service'; $entityId=(int)$id; $entityRow=$row; include __DIR__.'/../_seo_partial.php'; endif; ?>
</form>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
