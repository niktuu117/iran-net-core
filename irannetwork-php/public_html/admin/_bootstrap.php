<?php
/**
 * Admin shared bootstrap.
 * Include at the top of every admin/*.php file.
 *
 *   require_once __DIR__ . '/_bootstrap.php'; // adjust depth
 */
declare(strict_types=1);

// Resolve project root (public_html/)
$ROOT = realpath(__DIR__ . '/..');
if ($ROOT === false) { http_response_code(500); exit('Bootstrap path error'); }

// Load config
$configFile = $ROOT . '/app/config/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
} else {
    require_once $ROOT . '/app/config/config.example.php';
}

// Error reporting
if (defined('APP_ENV') && APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Session
if (session_status() === PHP_SESSION_NONE) {
    if (defined('SESSION_NAME')) session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
}

// Autoload core/controllers/models
spl_autoload_register(function (string $class) use ($ROOT): void {
    foreach (['core','controllers','models'] as $dir) {
        $f = $ROOT . '/app/' . $dir . '/' . $class . '.php';
        if (is_file($f)) { require $f; return; }
    }
});

require_once $ROOT . '/app/core/Helpers.php';

// Set view paths used by admin layout
$ADMIN_VIEWS = $ROOT . '/app/views/admin';
