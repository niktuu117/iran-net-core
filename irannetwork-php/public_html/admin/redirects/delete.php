<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/redirects/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) { (new Redirect())->delete($id); flash('success','ریدایرکت حذف شد.'); }
redirect('/admin/redirects/');
