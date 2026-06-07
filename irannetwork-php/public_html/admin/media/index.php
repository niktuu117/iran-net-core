<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();

$mediaModel = new Media();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    Csrf::check();
    $file = $_FILES['file'];
    // SVG intentionally NOT allowed — can carry inline <script>/XSS.
    $allowedExt  = ['jpg','jpeg','png','webp','gif'];
    $allowedMime = ['image/jpeg','image/png','image/webp','image/gif'];
    $maxSize = defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : 5 * 1024 * 1024;

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errors[] = 'خطا در آپلود فایل (کد ' . (int)$file['error'] . ').';
    } elseif ($file['size'] > $maxSize) {
        $errors[] = 'حجم فایل بیش از حد مجاز (' . round($maxSize/1024/1024,1) . ' MB) است.';
    } else {
        $orig = basename($file['name']);
        $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            $errors[] = 'پسوند فایل مجاز نیست. فقط: ' . implode(', ', $allowedExt);
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = (string)$finfo->file($file['tmp_name']);
            if (!in_array($mime, $allowedMime, true)) {
                $errors[] = 'نوع فایل مجاز نیست. (' . e($mime) . ')';
            }
        }
        if (!$errors) {
            $dir = defined('UPLOAD_DIR') ? UPLOAD_DIR : (__DIR__ . '/../../uploads/media');
            if (!is_dir($dir) && !@mkdir($dir, 0775, true)) $errors[] = 'امکان ساخت پوشه آپلود نیست.';
        }
        if (!$errors) {
            $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '-', pathinfo($orig, PATHINFO_FILENAME)) ?: 'file';
            $safe = trim($safe, '-') ?: 'file';
            $filename = $safe . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $filename;
            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $errors[] = 'انتقال فایل ناموفق بود.';
            } else {
                @chmod($target, 0644);
                $urlPrefix = defined('UPLOAD_URL') ? UPLOAD_URL : '/uploads/media';
                $mediaModel->create([
                    'title' => trim((string)($_POST['title'] ?? '')) ?: null,
                    'alt'   => trim((string)($_POST['alt'] ?? '')) ?: null,
                    'caption' => trim((string)($_POST['caption'] ?? '')) ?: null,
                    'filename' => $filename,
                    'original_name' => $orig,
                    'mime_type' => $mime ?? '',
                    'size' => (int)$file['size'],
                    'url' => rtrim($urlPrefix,'/') . '/' . $filename,
                ]);
                flash('success', 'فایل با موفقیت آپلود شد.');
                redirect('/admin/media/');
            }
        }
    }
}

$rows = $mediaModel->paginate(max(1,(int)($_GET['page'] ?? 1)), 30);
$pageTitle = 'رسانه‌ها';
ob_start(); ?>

<form class="card" method="post" enctype="multipart/form-data">
  <?= Csrf::field() ?>
  <h2><?= icon_svg('plus',16) ?> آپلود فایل جدید</h2>
  <?php foreach($errors as $err):?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
  <div class="form-grid">
    <div class="form-group full"><label>فایل (jpg, png, webp, gif — حداکثر <?= round((defined('MAX_UPLOAD_SIZE')?MAX_UPLOAD_SIZE:5242880)/1024/1024,1) ?> MB)</label>
      <input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.gif" required></div>
    <div class="form-group"><label>عنوان (اختیاری)</label><input name="title" maxlength="255"></div>
    <div class="form-group"><label>متن جایگزین (alt)</label><input name="alt" maxlength="255"></div>
    <div class="form-group full"><label>توضیح (caption)</label><textarea name="caption" rows="2"></textarea></div>
  </div>
  <div class="form-actions"><button class="btn btn-primary">آپلود</button></div>
</form>

<div class="card">
  <div class="card-head"><h2>کتابخانه رسانه (<?= (int)$rows['total'] ?>)</h2></div>
  <div class="media-grid">
    <?php if(!$rows['data']):?><p style="color:var(--muted)">فایلی آپلود نشده.</p><?php endif; ?>
    <?php foreach($rows['data'] as $m): ?>
      <div class="media-item">
        <?php $isImg = str_starts_with((string)$m['mime_type'],'image/'); ?>
        <?php if ($isImg): ?>
          <img src="<?= e($m['url']) ?>" alt="<?= e($m['alt'] ?? '') ?>" loading="lazy">
        <?php else: ?>
          <div style="height:130px;display:grid;place-items:center;color:var(--muted)"><?= icon_svg('doc',32) ?></div>
        <?php endif; ?>
        <div class="meta">
          <span title="<?= e($m['filename']) ?>"><?= e(mb_substr($m['filename'],0,18)) ?>…</span>
          <button class="copy-btn btn btn-secondary btn-sm" type="button" data-copy="<?= e($m['url']) ?>">کپی URL</button>
        </div>
        <div class="meta">
          <a href="/admin/media/edit.php?id=<?= (int)$m['id'] ?>">ویرایش</a>
          <form method="post" action="/admin/media/delete.php" data-confirm="حذف شود؟" style="display:inline">
            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
            <button class="btn btn-danger btn-sm">حذف</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php $content=ob_get_clean(); require __DIR__.'/../_layout.php';
