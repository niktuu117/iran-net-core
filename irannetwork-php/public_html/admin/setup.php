<?php
/**
 * First-admin setup. Available only when users table is empty.
 */
require_once __DIR__ . '/_bootstrap.php';

if (!Database::isConfigured()) {
    http_response_code(500);
    exit('ابتدا app/config/config.php را تنظیم کنید (اطلاعات دیتابیس).');
}

$userModel = new User();
try {
    if ($userModel->hasAny()) {
        redirect('/admin/login.php');
    }
} catch (Throwable $e) {
    http_response_code(500);
    exit('خطا در اتصال به دیتابیس. آیا schema.sql را import کرده‌اید؟<br>' . e($e->getMessage()));
}

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $name     = trim((string)($_POST['name'] ?? ''));
    $email    = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirm'] ?? '');

    if (mb_strlen($name) < 2)  $errors[] = 'نام باید حداقل ۲ کاراکتر باشد.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'ایمیل معتبر نیست.';
    if (strlen($password) < 8) $errors[] = 'رمز عبور باید حداقل ۸ کاراکتر باشد.';
    if ($password !== $confirm) $errors[] = 'تکرار رمز عبور مطابقت ندارد.';

    if (!$errors) {
        $userModel->create([
            'name' => $name, 'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'admin', 'status' => 'active',
        ]);
        flash('success', 'حساب مدیر با موفقیت ساخته شد. اکنون وارد شوید.');
        redirect('/admin/login.php');
    }
}
?><!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>راه‌اندازی اولیه — ایران نتورک</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="auth-shell">
  <div class="auth-card">
    <h1>راه‌اندازی اولین مدیر</h1>
    <p class="lead">این صفحه فقط یک بار قابل اجراست. اطلاعات اولین حساب مدیر را وارد کنید.</p>
    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= e($err) ?></div>
    <?php endforeach; ?>
    <form method="post" novalidate>
        <?= Csrf::field() ?>
        <div class="form-group" style="margin-bottom:14px">
            <label for="name">نام و نام خانوادگی</label>
            <input id="name" name="name" type="text" required minlength="2" maxlength="120" value="<?= e($name) ?>">
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label for="email">ایمیل</label>
            <input id="email" name="email" type="email" required maxlength="190" value="<?= e($email) ?>">
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label for="password">رمز عبور (حداقل ۸ کاراکتر)</label>
            <input id="password" name="password" type="password" required minlength="8">
        </div>
        <div class="form-group" style="margin-bottom:18px">
            <label for="confirm">تکرار رمز عبور</label>
            <input id="confirm" name="confirm" type="password" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">ساخت حساب مدیر</button>
    </form>
  </div>
</div>
</body>
</html>
