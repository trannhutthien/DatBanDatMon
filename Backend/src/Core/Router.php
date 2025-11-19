<?php
/**
 * Router Class
 * Simple routing system
 */

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function run(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Handle OPTIONS for CORS
        if ($requestMethod === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        foreach ($this->routes as $route) {
            $pattern = $this->convertPathToRegex($route['path']);
            
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match
                $this->executeHandler($route['handler'], $matches);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }

    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function executeHandler(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$controller, $method] = $handler;
            $controllerInstance = new $controller();
            call_user_func_array([$controllerInstance, $method], $params);
        } else {
            call_user_func_array($handler, $params);
        }
    }
}
