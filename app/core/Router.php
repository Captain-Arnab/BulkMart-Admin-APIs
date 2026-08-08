<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, callable|array $handler, array $middleware = []): self
    {
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        $this->routes[] = [
            'method'     => strtoupper($method),
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Strip base URL prefix (e.g. /VGS/veggiicart/public)
        $base = app_base_url();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if ($route['path'] !== $path) {
                continue;
            }

            foreach ($route['middleware'] as $mw) {
                if (is_callable($mw)) {
                    $mw();
                } elseif (is_string($mw) && function_exists($mw)) {
                    $mw();
                }
            }

            $handler = $route['handler'];
            if (is_array($handler)) {
                [$class, $action] = $handler;
                $controller = is_object($class) ? $class : new $class();
                $controller->$action();
                return;
            }

            $handler();
            return;
        }

        http_response_code(404);
        if (is_file(VIEW_PATH . '/errors/404.php')) {
            require VIEW_PATH . '/errors/404.php';
        } else {
            echo '404 Not Found';
        }
    }
}
