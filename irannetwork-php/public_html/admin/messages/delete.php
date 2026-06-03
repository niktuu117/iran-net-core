<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/messages/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) { (new ContactMessage())->delete($id); flash('success','پیام حذف شد.'); }
redirect('/admin/messages/');
