<?php
require_once __DIR__.'/../_bootstrap.php'; Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('/admin/media/');
Csrf::check(); $id=(int)($_POST['id']??0);
if ($id>0) {
    $m = new Media();
    $row = $m->find($id);
    if ($row) {
        $dir = defined('UPLOAD_DIR') ? UPLOAD_DIR : (__DIR__.'/../../uploads/media');
        $path = rtrim($dir,'/\\') . DIRECTORY_SEPARATOR . $row['filename'];
        if (is_file($path)) @unlink($path);
        $m->delete($id);
        flash('success','فایل حذف شد.');
    }
}
redirect('/admin/media/');
