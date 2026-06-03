<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/faqs/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) { (new Faq())->delete($id); flash('success','حذف شد.'); }
redirect('/admin/faqs/');
