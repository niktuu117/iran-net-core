<?php
declare(strict_types=1);

/**
 * Router — supports static + dynamic {param} routes and a global
 * redirect table lookup before dispatch.
 */
class Router
{
    /** @var array<string, array<int, array{pattern:string,keys:array<int,string>,handler:array{0:string,1:string}|callable}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, $handler): void  { $this->add('GET',  $path, $handler); }
    public function post(string $path, $handler): void { $this->add('POST', $path, $handler); }

    private function add(string $method, string $path, $handler): void
    {
        $path = $this->normalize($path);
        $keys = [];
        // Convert {slug} → ([^/]+)
        $pattern = preg_replace_callback('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', function ($m) use (&$keys) {
            $keys[] = $m[1];
            return '([^/]+)';
        }, $path);
        $pattern = '#^' . $pattern . '$#u';
        $this->routes[$method][] = ['pattern' => $pattern, 'keys' => $keys, 'handler' => $handler];
    }

    public function dispatch(string $url): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path   = $this->normalize($url);

        // 1) Redirect lookup (DB-backed). Silent on errors.
        try {
            if (class_exists('Redirect') && Database::isConfigured()) {
                $hit = (new Redirect())->findActive($path);
                if ($hit) {
                    $code = in_array((int)$hit['status_code'], [301, 302, 307, 308], true) ? (int)$hit['status_code'] : 301;
                    header('Location: ' . $hit['new_url'], true, $code);
                    exit;
                }
            }
        } catch (Throwable $e) { /* ignore in production */ }

        // 2) Route match
        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $m)) {
                $params = [];
                foreach ($route['keys'] as $i => $key) {
                    $params[$key] = rawurldecode($m[$i + 1] ?? '');
                }
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        $this->renderNotFound();
    }

    /** @param array{0:string,1:string}|callable $handler */
    private function invoke($handler, array $params): void
    {
        if (is_array($handler) && count($handler) === 2) {
            [$controllerName, $action] = $handler;
            if (!class_exists($controllerName)) { $this->renderNotFound(); return; }
            $controller = new $controllerName();
            if (!method_exists($controller, $action)) { $this->renderNotFound(); return; }
            $controller->{$action}($params);
            return;
        }
        if (is_callable($handler)) {
            $handler($params);
            return;
        }
        $this->renderNotFound();
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);
        if (class_exists('PagesController')) {
            (new PagesController())->notFound();
        } else {
            echo '404 - Not Found';
        }
    }
}
