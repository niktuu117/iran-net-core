<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
if (!Auth::can('manage_settings')) { http_response_code(403); exit('دسترسی غیرمجاز.'); }

$s = new SiteSetting();

$groups = [
    'عمومی' => [
        'site_name'        => ['نام سایت', 'text'],
        'site_description' => ['توضیح کوتاه سایت', 'textarea'],
        'site_url'         => ['آدرس کامل سایت (https://...)', 'ltr'],
        'default_og_image' => ['تصویر پیش‌فرض اشتراک‌گذاری', 'ltr'],
    ],
    'تماس' => [
        'phone_tehran'  => ['تلفن تهران', 'ltr'],
        'phone_isfahan' => ['تلفن اصفهان', 'ltr'],
        'email'         => ['ایمیل', 'ltr'],
    ],
    'دفتر تهران' => [
        'office_tehran_title'   => ['عنوان', 'text'],
        'office_tehran_address' => ['آدرس', 'textarea'],
        'office_tehran_phone'   => ['شماره تماس', 'ltr'],
        'office_tehran_lat'     => ['عرض جغرافیایی (lat)', 'ltr'],
        'office_tehran_lng'     => ['طول جغرافیایی (lng)', 'ltr'],
    ],
    'دفتر اصفهان' => [
        'office_isfahan_title'   => ['عنوان', 'text'],
        'office_isfahan_address' => ['آدرس', 'textarea'],
        'office_isfahan_phone'   => ['شماره تماس', 'ltr'],
        'office_isfahan_lat'     => ['عرض جغرافیایی (lat)', 'ltr'],
        'office_isfahan_lng'     => ['طول جغرافیایی (lng)', 'ltr'],
    ],
    'شبکه‌های اجتماعی' => [
        'social_instagram' => ['اینستاگرام (URL)', 'ltr'],
        'social_telegram'  => ['تلگرام (URL یا username)', 'ltr'],
        'social_whatsapp'  => ['واتساپ (URL یا شماره)', 'ltr'],
        'social_linkedin'  => ['لینکدین (URL)', 'ltr'],
        'social_aparat'    => ['آپارات (URL)', 'ltr'],
        'social_youtube'   => ['یوتیوب (URL)', 'ltr'],
        'social_twitter'   => ['توییتر / X (URL)', 'ltr'],
    ],
];

// Flatten keys
$allKeys = [];
foreach ($groups as $g => $fields) foreach ($fields as $k => $meta) $allKeys[] = $k;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    foreach ($allKeys as $k) {
        $val = isset($_POST[$k]) ? trim((string)$_POST[$k]) : '';
        $s->set($k, $val);
    }
    flash('success', 'تنظیمات ذخیره شد.');
    redirect('/admin/settings/');
}

$current   = $s->allAsMap();
$pageTitle = 'تنظیمات سایت';
ob_start(); ?>
<form method="post">
  <?= Csrf::field() ?>

  <?php foreach ($groups as $groupTitle => $fields): ?>
    <div class="card" style="margin-bottom:18px">
      <h2 style="margin-bottom:14px"><?= e($groupTitle) ?></h2>
      <div class="form-grid">
        <?php foreach ($fields as $k => [$label, $type]):
          $isTextarea = $type === 'textarea';
          $isLtr      = $type === 'ltr';
        ?>
          <div class="form-group <?= $isTextarea ? 'full' : '' ?>">
            <label for="<?= e($k) ?>"><?= e($label) ?></label>
            <?php if ($isTextarea): ?>
              <textarea id="<?= e($k) ?>" name="<?= e($k) ?>" rows="2"><?= e($current[$k] ?? '') ?></textarea>
            <?php else: ?>
              <input id="<?= e($k) ?>" name="<?= e($k) ?>"
                     value="<?= e($current[$k] ?? '') ?>"
                     <?= $isLtr ? 'dir="ltr"' : '' ?>>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <div class="form-actions">
    <button class="btn btn-primary">ذخیره تنظیمات</button>
  </div>
</form>

<p style="margin-top:14px;color:var(--muted);font-size:13px">
  راهنما: برای دریافت lat/lng، در Google Maps روی محل کلیک راست کنید و مختصات را کپی کنید.
</p>
<?php $content = ob_get_clean(); require __DIR__ . '/../_layout.php';
