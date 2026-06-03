<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/pages/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) { (new Page())->delete($id); flash('success','صفحه حذف شد.'); }
redirect('/admin/pages/');
