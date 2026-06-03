<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
$s = new SiteSetting();
$keys = ['site_name','site_description','phone_tehran','phone_isfahan','email',
         'address_tehran','address_isfahan','instagram','linkedin','whatsapp'];
$labels = [
    'site_name'=>'نام سایت','site_description'=>'توضیح کوتاه سایت',
    'phone_tehran'=>'تلفن تهران','phone_isfahan'=>'تلفن اصفهان','email'=>'ایمیل',
    'address_tehran'=>'آدرس تهران','address_isfahan'=>'آدرس اصفهان',
    'instagram'=>'اینستاگرام (URL)','linkedin'=>'لینکدین (URL)','whatsapp'=>'واتساپ (URL یا شماره)',
];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    Csrf::check();
    foreach ($keys as $k) {
        $val = isset($_POST[$k]) ? trim((string)$_POST[$k]) : '';
        $s->set($k, $val);
    }
    flash('success', 'تنظیمات ذخیره شد.');
    redirect('/admin/settings/');
}

$current = $s->allAsMap();
$pageTitle='تنظیمات سایت';
ob_start(); ?>
<form method="post" class="card">
  <?= Csrf::field() ?>
  <h2>تنظیمات عمومی سایت</h2>
  <div class="form-grid">
    <?php foreach ($keys as $k): $isTextarea = in_array($k, ['site_description','address_tehran','address_isfahan'], true); ?>
      <div class="form-group <?= $isTextarea?'full':'' ?>">
        <label for="<?= e($k) ?>"><?= e($labels[$k]) ?></label>
        <?php if ($isTextarea): ?>
          <textarea id="<?= e($k) ?>" name="<?= e($k) ?>" rows="2"><?= e($current[$k] ?? '') ?></textarea>
        <?php else: ?>
          <input id="<?= e($k) ?>" name="<?= e($k) ?>" value="<?= e($current[$k] ?? '') ?>"
                 <?= in_array($k,['email','instagram','linkedin','whatsapp'],true)?'dir="ltr"':'' ?>>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="form-actions"><button class="btn btn-primary">ذخیره تنظیمات</button></div>
</form>
<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
