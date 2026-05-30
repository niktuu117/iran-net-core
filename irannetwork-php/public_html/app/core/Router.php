<?php
declare(strict_types=1);

/**
 * Minimal PHP Router.
 * Supports static GET/POST routes. Dynamic params can be added later (Phase 3).
 */
class Router
{
    /** @var array<string, array<string, array{0:string,1:string}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $url): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path   = $this->normalize($url);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            $this->renderNotFound();
            return;
        }

        [$controllerName, $action] = $handler;

        if (!class_exists($controllerName)) {
            $this->renderNotFound();
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            $this->renderNotFound();
            return;
        }

        $controller->{$action}();
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
