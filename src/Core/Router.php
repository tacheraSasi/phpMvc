<?php

namespace Viper\Core;

use Viper\Http\Request;
use Viper\Http\Response;
use Viper\Middleware\MiddlewareManager;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Enhanced Router with middleware support
 */
class Router
{
    protected array $routes = [];
    protected MiddlewareManager $middlewareManager;
    protected ?Route $currentRoute = null;
    
    public function __construct()
    {
        $this->middlewareManager = new MiddlewareManager();
    }
    
    /**
     * Add GET route
     */
    public function get(string $uri, $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }
    
    /**
     * Add POST route
     */
    public function post(string $uri, $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }
    
    /**
     * Add PUT route
     */
    public function put(string $uri, $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }
    
    /**
     * Add DELETE route
     */
    public function delete(string $uri, $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }
    
    /**
     * Add route with any method
     */
    public function any(string $uri, $action): Route
    {
        $route = $this->addRoute('*', $uri, $action);
        return $route;
    }
    
    /**
     * Add route
     */
    protected function addRoute(string $method, string $uri, $action): Route
    {
        $route = new Route($method, $uri, $action);
        $this->routes[] = $route;
        return $route;
    }
    
    /**
     * Add global middleware
     */
    public function middleware(string|array $middleware): self
    {
        if (is_array($middleware)) {
            foreach ($middleware as $mw) {
                $this->middlewareManager->addGlobal($mw);
            }
        } else {
            $this->middlewareManager->addGlobal($middleware);
        }
        
        return $this;
    }
    
    /**
     * Register middleware alias
     */
    public function aliasMiddleware(string $name, string $class): self
    {
        $this->middlewareManager->alias($name, $class);
        return $this;
    }
    
    /**
     * Dispatch request
     */
    public function dispatch(Request $request): Response
    {
        $uri = $request->uri();
        $method = $request->method();
        
        // Find matching route
        $route = $this->findRoute($uri, $method);
        
        if (!$route) {
            return Response::error('Route not found', 404);
        }
        
        $this->currentRoute = $route;
        
        // Set route middleware
        $this->middlewareManager->setRouteMiddleware($route->getMiddleware());
        
        // Create controller callable
        $controller = $this->createController($route->getAction());
        
        // Process through middleware
        $response = $this->middlewareManager->process($request, $controller);
        
        // Clear route middleware for next request
        $this->middlewareManager->clearRouteMiddleware();
        
        return $response;
    }
    
    /**
     * Find matching route
     */
    protected function findRoute(string $uri, string $method): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->matches($uri, $method)) {
                return $route;
            }
        }
        
        return null;
    }
    
    /**
     * Create controller callable
     */
    protected function createController($action): callable
    {
        if (is_callable($action)) {
            return $action;
        }
        
        if (is_array($action) && count($action) === 2) {
            [$controllerClass, $method] = $action;
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                if (method_exists($controller, $method)) {
                    return [$controller, $method];
                }
            }
        }
        
        throw new \Exception('Invalid controller action');
    }
    
    /**
     * Get middleware manager
     */
    public function getMiddlewareManager(): MiddlewareManager
    {
        return $this->middlewareManager;
    }
}