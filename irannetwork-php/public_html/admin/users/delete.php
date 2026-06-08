<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
if (!Auth::can('manage_users')) { http_response_code(403); exit('دسترسی غیرمجاز.'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/users/');
Csrf::check();

$id = (int)($_POST['id'] ?? 0);
$m  = new User();
$u  = $m->find($id);
if (!$u) { flash('error','کاربر یافت نشد.'); redirect('/admin/users/'); }

if ((int)$u['id'] === Auth::id())       { flash('error','نمی‌توانید حساب خودتان را حذف کنید.'); redirect('/admin/users/'); }
if ($u['role'] === 'super_admin')       { flash('error','سوپر ادمین قابل حذف نیست.'); redirect('/admin/users/'); }

$m->delete($id);
flash('success','کاربر حذف شد.');
redirect('/admin/users/');
