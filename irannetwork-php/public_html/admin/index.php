<?php
require_once __DIR__ . '/_bootstrap.php';
redirect(Auth::check() && Auth::isAdmin() ? '/admin/dashboard.php' : '/admin/login.php');
