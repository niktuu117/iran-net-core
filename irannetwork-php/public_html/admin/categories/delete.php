<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/categories/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) { (new Category())->delete($id); flash('success','دسته حذف شد.'); }
redirect('/admin/categories/');
