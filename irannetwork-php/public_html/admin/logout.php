<?php
require_once __DIR__ . '/_bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') Csrf::check();
Auth::logout();
redirect('/admin/login.php');
