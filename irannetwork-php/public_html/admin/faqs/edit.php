<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$m = new Faq();
$isEdit = isset($_GET['id']) || (int)($_POST['id']??0)>0;
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$row = ['question'=>'','answer'=>'','sort_order'=>0,'is_active'=>1,'post_id'=>null,'service_id'=>null,'page_id'=>null];
if ($isEdit) {
    $found = $m->find($id);
    if (!$found) { flash('error','یافت نشد.'); redirect('/admin/faqs/'); }
    $row = $found;
}
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    Csrf::check();
    $data = [
        'question'=>trim((string)($_POST['question']??'')),
        'answer'  =>sanitize_html((string)($_POST['answer']??'')),
        'sort_order'=>(int)($_POST['sort_order']??0),
        'is_active'=>!empty($_POST['is_active'])?1:0,
        'post_id'   =>($_POST['post_id']   ?? '') !== '' ? (int)$_POST['post_id']    : null,
        'service_id'=>($_POST['service_id']?? '') !== '' ? (int)$_POST['service_id'] : null,
        'page_id'   =>($_POST['page_id']   ?? '') !== '' ? (int)$_POST['page_id']    : null,
    ];
    if (mb_strlen($data['question'])<3) $errors[]='پرسش الزامی است.';
    if (mb_strlen(strip_tags($data['answer']))<3) $errors[]='پاسخ الزامی است.';
    if (!$errors) {
        if ($isEdit) $m->update($id,$data); else $id=$m->create($data);
        flash('success', $isEdit?'به‌روزرسانی شد.':'ساخته شد.');
        redirect('/admin/faqs/edit.php?id='.$id);
    }
    $row = array_merge($row,$data);
}
$posts    = Database::fetchAll('SELECT id,title FROM posts ORDER BY id DESC LIMIT 200');
$services = Database::fetchAll('SELECT id,title FROM services ORDER BY sort_order ASC');
$pages    = Database::fetchAll('SELECT id,title FROM pages ORDER BY id ASC');
$pageTitle = $isEdit?'ویرایش سوال':'سوال جدید';
ob_start(); ?>
<form method="post" class="card">
  <?= Csrf::field() ?><?php if($isEdit):?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
  <?php foreach($errors as $err):?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
  <div class="form-grid">
    <div class="form-group full"><label>پرسش *</label><input name="question" required maxlength="500" value="<?= e($row['question']) ?>"></div>
    <div class="form-group full"><label>پاسخ *</label><textarea name="answer" class="js-editor" rows="8"><?= e($row['answer']) ?></textarea></div>
    <div class="form-group"><label>ترتیب</label><input name="sort_order" type="number" value="<?= (int)$row['sort_order'] ?>"></div>
    <div class="form-group"><label>فعال؟</label><label><input type="checkbox" name="is_active" value="1" <?= !empty($row['is_active'])?'checked':'' ?>> فعال است</label></div>
    <div class="form-group"><label>اتصال به مقاله</label><select name="post_id"><option value="">— هیچ —</option>
      <?php foreach($posts as $p):?><option value="<?= (int)$p['id'] ?>" <?= ((int)$row['post_id']===(int)$p['id'])?'selected':'' ?>><?= e($p['title']) ?></option><?php endforeach; ?>
    </select></div>
    <div class="form-group"><label>اتصال به سرویس</label><select name="service_id"><option value="">— هیچ —</option>
      <?php foreach($services as $s):?><option value="<?= (int)$s['id'] ?>" <?= ((int)$row['service_id']===(int)$s['id'])?'selected':'' ?>><?= e($s['title']) ?></option><?php endforeach; ?>
    </select></div>
    <div class="form-group"><label>اتصال به صفحه</label><select name="page_id"><option value="">— هیچ —</option>
      <?php foreach($pages as $pg):?><option value="<?= (int)$pg['id'] ?>" <?= ((int)$row['page_id']===(int)$pg['id'])?'selected':'' ?>><?= e($pg['title']) ?></option><?php endforeach; ?>
    </select></div>
  </div>
  <div class="form-actions">
    <a class="btn btn-ghost" href="/admin/faqs/">انصراف</a>
    <button class="btn btn-primary"><?= $isEdit?'به‌روزرسانی':'ذخیره' ?></button>
  </div>
</form>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
