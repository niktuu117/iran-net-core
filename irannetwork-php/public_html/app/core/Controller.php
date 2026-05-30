<?php
declare(strict_types=1);

/**
 * Base Controller — renders views inside a layout.
 */
class Controller
{
    /**
     * Render a view inside the main layout.
     *
     * @param string               $view  e.g. 'public/home'
     * @param array<string, mixed> $data  variables for the view
     * @param string               $layout e.g. 'main'
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewFile   = __DIR__ . '/../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        // Extract data into view scope
        extract($data, EXTR_SKIP);

        // Render view into buffer, then inject into layout
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if (is_file($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}
