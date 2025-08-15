<?php

namespace Viper\Core;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Route class representing a single route
 */
class Route
{
    protected string $method;
    protected string $uri;
    protected string $pattern;
    protected $action; // Remove union type for PHP compatibility
    protected array $middleware = [];
    protected array $parameters = [];
    
    public function __construct(string $method, string $uri, $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->pattern = $this->compilePattern($uri);
    }
    
    /**
     * Add middleware to this route
     */
    public function middleware(string|array $middleware): self
    {
        if (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware, $middleware);
        } else {
            $this->middleware[] = $middleware;
        }
        
        return $this;
    }
    
    /**
     * Check if route matches URI and method
     */
    public function matches(string $uri, string $method): bool
    {
        // Check method
        if ($this->method !== '*' && $this->method !== $method) {
            return false;
        }
        
        // Clean URIs for comparison
        $routeUri = trim($this->uri, '/');
        $requestUri = trim($uri, '/');
        
        // Simple string matching
        if ($routeUri === $requestUri) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Compile URI pattern for regex matching
     */
    protected function compilePattern(string $uri): string
    {
        // Handle exact matches first
        if (strpos($uri, '{') === false) {
            return '/^' . preg_quote($uri, '/') . '$/';
        }
        
        // Escape special regex characters but preserve { }
        $pattern = str_replace(['\\{', '\\}'], ['{', '}'], preg_quote($uri, '/'));
        
        // Replace parameter placeholders {param} with regex groups
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        
        // Add start/end anchors
        return '/^' . $pattern . '$/';
    }
    
    /**
     * Get route action
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Get route middleware
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }
    
    /**
     * Get route parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    /**
     * Get route method
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * Get route URI
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}