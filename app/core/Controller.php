<?php

class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        extract($data, EXTR_SKIP);
        $contentView = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($contentView)) {
            http_response_code(500);
            echo 'View not found: ' . e($view);
            return;
        }

        if ($layout === null || $layout === false || $layout === '') {
            require $contentView;
            return;
        }

        $layoutFile = VIEW_PATH . '/' . str_replace('.', '/', $layout) . '.php';
        if (!is_file($layoutFile)) {
            require $contentView;
            return;
        }

        ob_start();
        require $contentView;
        $content = ob_get_clean();
        require $layoutFile;
    }

    protected function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
