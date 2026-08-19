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

    public function put(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('PUT', $path, $handler, $middleware);
    }

    public function patch(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('PATCH', $path, $handler, $middleware);
    }

    public function delete(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('DELETE', $path, $handler, $middleware);
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
        // Method override for clients that can't send PUT/DELETE
        if ($method === 'POST') {
            $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? ($_POST['_method'] ?? '');
            if (is_string($override) && $override !== '') {
                $method = strtoupper($override);
            }
        }

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        $base = app_base_url();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        // Website now occupies "/", so /api/v1 may arrive without the /public prefix.
        $apiAt = strpos($path, '/api/');
        if ($apiAt !== false) {
            $path = substr($path, $apiAt);
        }

        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $isApi = str_starts_with($path, '/api/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $path);
            if ($params === null) {
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
                $controller->$action(...array_values($params));
                return;
            }

            $handler(...array_values($params));
            return;
        }

        http_response_code(404);
        if ($isApi) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'data'    => null,
                'error'   => ['code' => 'NOT_FOUND', 'message' => 'Endpoint not found.'],
            ], JSON_UNESCAPED_SLASHES);
            return;
        }

        if (is_file(VIEW_PATH . '/errors/404.php')) {
            require VIEW_PATH . '/errors/404.php';
        } else {
            echo '404 Not Found';
        }
    }

    /** @return array<string,string>|null */
    private function match(string $routePath, string $requestPath): ?array
    {
        if ($routePath === $requestPath) {
            return [];
        }

        $routeParts = explode('/', trim($routePath, '/'));
        $reqParts = explode('/', trim($requestPath, '/'));
        if (count($routeParts) !== count($reqParts)) {
            return null;
        }

        $params = [];
        foreach ($routeParts as $i => $part) {
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $part, $m)) {
                $params[$m[1]] = $reqParts[$i];
                continue;
            }
            if ($part !== $reqParts[$i]) {
                return null;
            }
        }
        return $params;
    }
}
