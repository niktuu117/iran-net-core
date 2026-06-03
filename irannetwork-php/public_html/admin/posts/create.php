<?php
require_once __DIR__ . '/../_bootstrap.php';
Auth::requireAdmin();
// alias to edit.php in "create" mode
require __DIR__ . '/edit.php';
