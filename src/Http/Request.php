<?php

namespace Viper\Http;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Enhanced Request class with helpers for query params, body, headers, cookies
 */
class Request
{
    protected array $headers = [];
    protected array $queryParams = [];
    protected array $bodyParams = [];
    protected string $body = '';
    protected array $files = [];
    protected array $cookies = [];
    protected string $method = '';
    protected string $uri = '';
    
    public function __construct()
    {
        $this->parseRequest();
    }
    
    protected function parseRequest(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->parseUri();
        $this->headers = $this->parseHeaders();
        $this->queryParams = $_GET ?? [];
        $this->cookies = $_COOKIE ?? [];
        $this->files = $_FILES ?? [];
        
        $this->parseBody();
    }
    
    protected function parseUri(): string
    {
        $uri = $_GET['url'] ?? 'home';
        return trim($uri, '/');
    }
    
    protected function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$headerName] = $value;
            }
        }
        
        // Add common headers
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }
        
        return $headers;
    }
    
    protected function parseBody(): void
    {
        if ($this->method === 'POST') {
            $contentType = $this->header('Content-Type', '');
            
            if (strpos($contentType, 'application/json') !== false) {
                $this->body = file_get_contents('php://input');
                $decoded = json_decode($this->body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->bodyParams = $decoded;
                }
            } else {
                $this->bodyParams = $_POST ?? [];
            }
        }
    }
    
    /**
     * Get request method
     */
    public function method(): string
    {
        return $this->method;
    }
    
    /**
     * Get request URI
     */
    public function uri(): string
    {
        return $this->uri;
    }
    
    /**
     * Get query parameter
     */
    public function query(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->queryParams;
        }
        
        return $this->queryParams[$key] ?? $default;
    }
    
    /**
     * Get body parameter
     */
    public function input(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->bodyParams;
        }
        
        return $this->bodyParams[$key] ?? $default;
    }
    
    /**
     * Get all input data (query + body)
     */
    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }
    
    /**
     * Get raw request body
     */
    public function body(): string
    {
        return $this->body;
    }
    
    /**
     * Get header value
     */
    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[$key] ?? $default;
    }
    
    /**
     * Get all headers
     */
    public function headers(): array
    {
        return $this->headers;
    }
    
    /**
     * Get cookie value
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }
    
    /**
     * Get uploaded file
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }
    
    /**
     * Get all uploaded files
     */
    public function files(): array
    {
        return $this->files;
    }
    
    /**
     * Check if request has input
     */
    public function has(string $key): bool
    {
        return isset($this->bodyParams[$key]) || isset($this->queryParams[$key]);
    }
    
    /**
     * Check if request is JSON
     */
    public function isJson(): bool
    {
        return strpos($this->header('Content-Type', ''), 'application/json') !== false;
    }
    
    /**
     * Get client IP address
     */
    public function ip(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
               $_SERVER['HTTP_CLIENT_IP'] ?? 
               $_SERVER['REMOTE_ADDR'] ?? 
               'unknown';
    }
    
    /**
     * Get user agent
     */
    public function userAgent(): string
    {
        return $this->header('User-Agent', '');
    }
}