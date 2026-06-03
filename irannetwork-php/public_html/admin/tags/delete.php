<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/tags/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) { (new Tag())->delete($id); flash('success','برچسب حذف شد.'); }
redirect('/admin/tags/');
