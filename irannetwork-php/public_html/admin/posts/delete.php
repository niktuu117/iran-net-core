<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/posts/');
Csrf::check();
$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    (new Post())->delete($id);
    flash('success','مقاله حذف شد.');
}
redirect('/admin/posts/');
