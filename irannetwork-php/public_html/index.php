<?php
/**
 * IranNetwork - Front Controller (Phase 3)
 * Dynamic routes (DB-backed) + SEO module + redirects.
 */
declare(strict_types=1);

$configFile = __DIR__ . '/app/config/config.php';
if (file_exists($configFile)) { require $configFile; }
else { require __DIR__ . '/app/config/config.example.php'; }

if (defined('APP_ENV') && APP_ENV === 'development') {
    error_reporting(E_ALL); ini_set('display_errors', '1');
} else {
    error_reporting(0); ini_set('display_errors', '0');
}

session_set_cookie_params([
    'lifetime' => 0, 'path' => '/',
    'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax',
]);
if (defined('SESSION_NAME')) session_name(SESSION_NAME);
session_start();

spl_autoload_register(function (string $class): void {
    $base = __DIR__ . '/app/';
    foreach (['core/', 'controllers/', 'models/', 'helpers/'] as $dir) {
        $file = $base . $dir . $class . '.php';
        if (is_file($file)) { require $file; return; }
    }
});

require __DIR__ . '/app/core/Helpers.php';

$router = new Router();

// ---- Public dynamic routes ----
$router->get('/',                          ['PagesController', 'home']);

$router->get('/blog',                      ['BlogController', 'index']);
$router->get('/blog/{slug}',               ['BlogController', 'show']);

$router->get('/services',                  ['ServicesController', 'index']);
$router->get('/services/{slug}',           ['ServicesController', 'show']);

$router->get('/category/{slug}',           ['BlogController', 'byCategory']);
$router->get('/tag/{slug}',                ['BlogController', 'byTag']);

$router->get('/contact',                   ['PagesController', 'contact']);
$router->post('/contact',                  ['PagesController', 'submitContact']);

// SEO endpoints
$router->get('/sitemap.xml',               ['SeoController', 'sitemap']);
$router->get('/robots.txt',                ['SeoController', 'robots']);

// 404 explicit
$router->get('/404',                       ['PagesController', 'notFound']);

// Catch-all CMS page (MUST be last)
$router->get('/{slug}',                    ['PagesController', 'dynamicPage']);

$url = isset($_GET['_url']) ? '/' . trim((string)$_GET['_url'], '/') : '/';
$router->dispatch($url);
