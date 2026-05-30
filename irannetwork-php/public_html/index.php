<?php
/**
 * IranNetwork - Front Controller
 * Phase 1: Static pages only.
 */

declare(strict_types=1);

// Load config (falls back gracefully if config.php not yet created)
$configFile = __DIR__ . '/app/config/config.php';
if (file_exists($configFile)) {
    require $configFile;
} else {
    require __DIR__ . '/app/config/config.example.php';
}

// Error handling based on environment
if (defined('APP_ENV') && APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Secure session
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Autoload core classes (simple PSR-ish loader)
spl_autoload_register(function (string $class): void {
    $base = __DIR__ . '/app/';
    $candidates = [
        $base . 'core/' . $class . '.php',
        $base . 'controllers/' . $class . '.php',
        $base . 'models/' . $class . '.php',
        $base . 'helpers/' . $class . '.php',
    ];
    foreach ($candidates as $file) {
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

// Bootstrap router
$router = new Router();

// ---- Public routes (Phase 1: static) ----
$router->get('/',                              ['PagesController', 'home']);
$router->get('/about',                         ['PagesController', 'about']);
$router->get('/contact',                       ['PagesController', 'contact']);
$router->get('/faq',                           ['PagesController', 'faq']);
$router->get('/rules',                         ['PagesController', 'rules']);
$router->get('/blog',                          ['PagesController', 'blog']);

$router->get('/services',                              ['ServicesController', 'index']);
$router->get('/services/network-support',              ['ServicesController', 'networkSupport']);
$router->get('/services/network-installation',         ['ServicesController', 'networkInstallation']);
$router->get('/services/voip',                         ['ServicesController', 'voip']);
$router->get('/services/digital-marketing',            ['ServicesController', 'digitalMarketing']);
$router->get('/services/network-security',             ['ServicesController', 'networkSecurity']);
$router->get('/services/server-support',               ['ServicesController', 'serverSupport']);
$router->get('/services/active-network',               ['ServicesController', 'activeNetwork']);
$router->get('/services/passive-network',              ['ServicesController', 'passiveNetwork']);

$router->get('/404', ['PagesController', 'notFound']);

// Dispatch
$url = isset($_GET['_url']) ? '/' . trim((string)$_GET['_url'], '/') : '/';
$router->dispatch($url);
