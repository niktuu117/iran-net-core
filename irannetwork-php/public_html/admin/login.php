<?php
require_once __DIR__ . '/_bootstrap.php';

if (Auth::check() && Auth::isAdmin()) redirect('/admin/dashboard.php');

if (!Database::isConfigured()) {
    http_response_code(500);
    exit('ابتدا app/config/config.php را تنظیم کنید.');
}

// If no users yet, force setup
try {
    if (!(new User())->hasAny()) redirect('/admin/setup.php');
} catch (Throwable $e) {
    http_response_code(500);
    exit('خطا در اتصال به دیتابیس. schema.sql را import کنید.<br>' . e($e->getMessage()));
}

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    // Throttle: max 5 attempts / 15 minutes per IP
    $wait = Throttle::check('login', 5, 900);
    if ($wait > 0) {
        $error = 'تلاش‌های ناموفق زیاد بوده. لطفاً ' . ceil($wait / 60) . ' دقیقه دیگر امتحان کنید.';
    } else {
        $email = trim((string)($_POST['email'] ?? ''));
        $pwd   = (string)($_POST['password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pwd === '') {
            Throttle::hit('login', 900);
            $error = 'ایمیل یا رمز نامعتبر است.';
        } else {
            $user = Auth::attempt($email, $pwd);
            if ($user && ($user['role'] ?? '') === 'admin') {
                Throttle::clear('login');
                redirect('/admin/dashboard.php');
            }
            Throttle::hit('login', 900);
            $error = 'ایمیل یا رمز عبور اشتباه است.';
        }
    }
}

$flashSuccess = flash('success');
?><!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>ورود به پنل — ایران نتورک</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="auth-shell">
  <div class="auth-card">
    <h1>ورود به پنل مدیریت</h1>
    <p class="lead">برای دسترسی به مدیریت محتوای ایران نتورک وارد شوید.</p>
    <?php if ($flashSuccess): ?><div class="flash flash-success"><?= e($flashSuccess) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" novalidate>
      <?= Csrf::field() ?>
      <div class="form-group" style="margin-bottom:14px">
        <label for="email">ایمیل</label>
        <input id="email" name="email" type="email" required value="<?= e($email) ?>">
      </div>
      <div class="form-group" style="margin-bottom:18px">
        <label for="password">رمز عبور</label>
        <input id="password" name="password" type="password" required>
      </div>
      <button class="btn btn-primary" style="width:100%">ورود</button>
    </form>
  </div>
</div>
</body>
</html>
