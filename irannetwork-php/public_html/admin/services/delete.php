<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/services/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) { (new Service())->delete($id); flash('success','سرویس حذف شد.'); }
redirect('/admin/services/');
