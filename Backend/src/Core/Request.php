<?php
/**
 * Request Class
 * Handle HTTP requests
 */

namespace App\Core;

class Request
{
    private array $data;

    public function __construct()
    {
        $this->data = $this->parseInput();
    }

    private function parseInput(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'GET') {
            return $_GET;
        }
        
        if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            
            if (strpos($contentType, 'application/json') !== false) {
                $json = file_get_contents('php://input');
                return json_decode($json, true) ?? [];
            }
            
            return $_POST;
        }
        
        return [];
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function header(string $key): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? null;
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization');
        if ($header && preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
