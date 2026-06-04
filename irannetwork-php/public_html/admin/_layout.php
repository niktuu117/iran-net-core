<?php
/**
 * Admin layout wrapper.
 * Usage in a page:
 *   $pageTitle = 'عنوان';
 *   ob_start();
 *   // page HTML
 *   $content = ob_get_clean();
 *   require __DIR__ . '/_layout.php';
 */
$pageTitle = $pageTitle ?? 'پنل مدیریت ایران نتورک';
$user = Auth::user();
$cm   = new ContactMessage();
$unread = 0;
try { $unread = $cm->unreadCount(); } catch (Throwable $e) { $unread = 0; }
$flashSuccess = flash('success');
$flashError   = flash('error');
$cur = $_SERVER['REQUEST_URI'] ?? '';
$is = fn(string $p) => str_starts_with($cur, $p) ? 'active' : '';
?><!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle) ?> — پنل مدیریت ایران نتورک</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
<button class="admin-menu-toggle" id="adminMenuToggle" aria-label="منو">
    <?= icon_svg('menu', 22) ?>
</button>

<aside class="admin-sidebar" id="adminSidebar">
    <a href="/admin/dashboard.php" class="admin-brand">
        <span class="admin-brand-mark">IN</span>
        <span class="admin-brand-text">
            <strong>ایران نتورک</strong>
            <small>پنل مدیریت</small>
        </span>
    </a>
    <nav class="admin-nav">
        <a href="/admin/dashboard.php"   class="<?= $is('/admin/dashboard.php') ?>"><?= icon_svg('bolt',18) ?> داشبورد</a>
        <a href="/admin/posts/"          class="<?= $is('/admin/posts') ?>"><?= icon_svg('doc',18) ?> مقاله‌ها</a>
        <a href="/admin/services/"       class="<?= $is('/admin/services') ?>"><?= icon_svg('server',18) ?> سرویس‌ها</a>
        <a href="/admin/pages/"          class="<?= $is('/admin/pages') ?>"><?= icon_svg('folder',18) ?> صفحات</a>
        <a href="/admin/categories/"     class="<?= $is('/admin/categories') ?>"><?= icon_svg('folder',18) ?> دسته‌بندی‌ها</a>
        <a href="/admin/tags/"           class="<?= $is('/admin/tags') ?>"><?= icon_svg('tag',18) ?> برچسب‌ها</a>
        <a href="/admin/media/"          class="<?= $is('/admin/media') ?>"><?= icon_svg('image',18) ?> رسانه‌ها</a>
        <a href="/admin/faqs/"           class="<?= $is('/admin/faqs') ?>"><?= icon_svg('star',18) ?> سوالات متداول</a>
        <a href="/admin/messages/"       class="<?= $is('/admin/messages') ?>"><?= icon_svg('inbox',18) ?> پیام‌های تماس <?php if ($unread): ?><span class="badge"><?= (int)$unread ?></span><?php endif; ?></a>
        <a href="/admin/redirects/"      class="<?= $is('/admin/redirects') ?>"><?= icon_svg('arrow',18) ?> ریدایرکت‌ها</a>
        <a href="/admin/settings/"       class="<?= $is('/admin/settings') ?>"><?= icon_svg('settings',18) ?> تنظیمات سایت</a>
    </nav>
    <div class="admin-sidebar-foot">
        <a href="/" target="_blank" rel="noopener">مشاهده سایت ↗</a>
    </div>
</aside>

<div class="admin-main">
    <header class="admin-topbar">
        <h1 class="admin-page-title"><?= e($pageTitle) ?></h1>
        <div class="admin-user">
            <span><?= e($user['name'] ?? 'کاربر') ?></span>
            <form action="/admin/logout.php" method="post" style="display:inline">
                <?= Csrf::field() ?>
                <button type="submit" class="btn-link"><?= icon_svg('logout',16) ?> خروج</button>
            </form>
        </div>
    </header>

    <?php if ($flashSuccess): ?><div class="flash flash-success"><?= e($flashSuccess) ?></div><?php endif; ?>
    <?php if ($flashError):   ?><div class="flash flash-error"><?= e($flashError) ?></div><?php endif; ?>

    <main class="admin-content">
        <?= $content ?? '' ?>
    </main>

    <footer class="admin-footer">© <?= date('Y') ?> ایران نتورک</footer>
</div>

<script>
document.getElementById('adminMenuToggle')?.addEventListener('click', () => {
    document.getElementById('adminSidebar')?.classList.toggle('open');
});
</script>
<script src="/assets/js/admin.js" defer></script>
</body>
</html>
