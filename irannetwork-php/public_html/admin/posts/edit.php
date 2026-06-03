<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();

$postModel = new Post();
$catModel  = new Category();
$tagModel  = new Tag();
$svcModel  = new Service();
$mediaModel= new Media();

$isEdit = isset($_GET['id']) || (isset($_POST['id']) && (int)$_POST['id'] > 0);
$post = ['title'=>'','slug'=>'','excerpt'=>'','content'=>'','status'=>'draft','featured'=>0,'show_on_homepage'=>0,
         'published_at'=>'','scheduled_at'=>'','featured_image'=>'','featured_image_alt'=>'','category_id'=>null];
$postId = 0;
$selectedTags = []; $selectedServices = [];

if ($isEdit) {
    $postId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
    $row = $postModel->find($postId);
    if (!$row) { flash('error','مقاله یافت نشد.'); redirect('/admin/posts/'); }
    $post = $row;
    $selectedTags     = $postModel->getTagIds($postId);
    $selectedServices = $postModel->getServiceIds($postId);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $data = [
        'title'              => trim((string)($_POST['title'] ?? '')),
        'slug'               => trim((string)($_POST['slug'] ?? '')),
        'excerpt'            => trim((string)($_POST['excerpt'] ?? '')) ?: null,
        'content'            => sanitize_html((string)($_POST['content'] ?? '')),
        'status'             => in_array($_POST['status'] ?? '', ['draft','published','scheduled'], true) ? $_POST['status'] : 'draft',
        'featured'           => !empty($_POST['featured']) ? 1 : 0,
        'show_on_homepage'   => !empty($_POST['show_on_homepage']) ? 1 : 0,
        'published_at'       => ($_POST['published_at'] ?? '') ?: null,
        'scheduled_at'       => ($_POST['scheduled_at'] ?? '') ?: null,
        'featured_image'     => trim((string)($_POST['featured_image'] ?? '')) ?: null,
        'featured_image_alt' => trim((string)($_POST['featured_image_alt'] ?? '')) ?: null,
        'category_id'        => ($_POST['category_id'] ?? '') !== '' ? (int)$_POST['category_id'] : null,
        'author_id'          => Auth::id(),
    ];
    if (mb_strlen($data['title']) < 2) $errors[] = 'عنوان الزامی است.';
    if ($data['slug'] === '') $data['slug'] = slugify($data['title']);
    $data['slug'] = unique_slug(slugify($data['slug']), 'posts', $isEdit ? $postId : null);

    if (!$errors) {
        if ($isEdit) {
            $postModel->update($postId, $data);
        } else {
            $postId = $postModel->create($data);
        }
        $postModel->syncTags($postId, (array)($_POST['tags'] ?? []));
        $postModel->syncServices($postId, (array)($_POST['services'] ?? []));
        flash('success', $isEdit ? 'مقاله به‌روزرسانی شد.' : 'مقاله ساخته شد.');
        redirect('/admin/posts/edit.php?id=' . $postId);
    }
    $post = array_merge($post, $data);
    $selectedTags     = array_map('intval', (array)($_POST['tags'] ?? []));
    $selectedServices = array_map('intval', (array)($_POST['services'] ?? []));
}

$categories = $catModel->all('name ASC');
$tags       = $tagModel->all('name ASC');
$services   = $svcModel->all('sort_order ASC');
$mediaItems = $mediaModel->paginate(1, 50)['data'];

$pageTitle = $isEdit ? 'ویرایش مقاله' : 'مقاله جدید';
ob_start(); ?>
<form method="post" class="card">
  <?= Csrf::field() ?>
  <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$postId ?>"><?php endif; ?>

  <?php foreach ($errors as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>

  <div class="form-grid">
    <div class="form-group full">
      <label for="title">عنوان *</label>
      <input id="title" name="title" required maxlength="255" value="<?= e($post['title']) ?>">
    </div>
    <div class="form-group">
      <label for="slug">اسلاگ (URL)</label>
      <input id="slug" name="slug" maxlength="190" dir="ltr" value="<?= e($post['slug']) ?>" data-slug-from="title">
      <div class="form-help">در صورت خالی بودن، خودکار از عنوان ساخته می‌شود.</div>
    </div>
    <div class="form-group">
      <label for="status">وضعیت</label>
      <select id="status" name="status">
        <option value="draft"     <?= $post['status']==='draft'?'selected':'' ?>>پیش‌نویس</option>
        <option value="published" <?= $post['status']==='published'?'selected':'' ?>>منتشر شده</option>
        <option value="scheduled" <?= $post['status']==='scheduled'?'selected':'' ?>>زمان‌بندی شده</option>
      </select>
    </div>
    <div class="form-group full">
      <label for="excerpt">خلاصه</label>
      <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"><?= e($post['excerpt']) ?></textarea>
    </div>
    <div class="form-group full">
      <label for="content">محتوای مقاله</label>
      <textarea id="content" name="content" class="js-editor" rows="14"><?= e($post['content']) ?></textarea>
    </div>
    <div class="form-group">
      <label for="category_id">دسته‌بندی</label>
      <select id="category_id" name="category_id">
        <option value="">— بدون دسته —</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= ((int)$post['category_id']===(int)$c['id'])?'selected':'' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>تنظیمات نمایش</label>
      <label><input type="checkbox" name="featured" value="1" <?= !empty($post['featured'])?'checked':'' ?>> مقاله ویژه</label><br>
      <label><input type="checkbox" name="show_on_homepage" value="1" <?= !empty($post['show_on_homepage'])?'checked':'' ?>> نمایش در صفحه اصلی</label>
    </div>
    <div class="form-group">
      <label for="published_at">تاریخ انتشار</label>
      <input id="published_at" name="published_at" type="datetime-local" dir="ltr" value="<?= e($post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '') ?>">
    </div>
    <div class="form-group">
      <label for="scheduled_at">زمان‌بندی</label>
      <input id="scheduled_at" name="scheduled_at" type="datetime-local" dir="ltr" value="<?= e($post['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($post['scheduled_at'])) : '') ?>">
    </div>
    <div class="form-group">
      <label for="featured_image">تصویر شاخص (URL)</label>
      <input id="featured_image" name="featured_image" type="text" dir="ltr" value="<?= e($post['featured_image']) ?>">
      <div class="form-help">آدرس از بخش <a href="/admin/media/">رسانه‌ها</a> کپی کنید.</div>
    </div>
    <div class="form-group">
      <label for="featured_image_alt">متن جایگزین تصویر</label>
      <input id="featured_image_alt" name="featured_image_alt" maxlength="255" value="<?= e($post['featured_image_alt']) ?>">
    </div>
    <div class="form-group full">
      <label>برچسب‌ها</label>
      <select name="tags[]" multiple size="5">
        <?php foreach ($tags as $t): ?>
        <option value="<?= (int)$t['id'] ?>" <?= in_array((int)$t['id'],$selectedTags,true)?'selected':'' ?>><?= e($t['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group full">
      <label>سرویس‌های مرتبط</label>
      <select name="services[]" multiple size="5">
        <?php foreach ($services as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= in_array((int)$s['id'],$selectedServices,true)?'selected':'' ?>><?= e($s['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-actions">
    <a href="/admin/posts/" class="btn btn-ghost">انصراف</a>
    <button class="btn btn-primary" type="submit"><?= $isEdit ? 'به‌روزرسانی' : 'ذخیره مقاله' ?></button>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../_layout.php';
