<?php

namespace Viper\Middleware;

use Viper\Http\Request;
use Viper\Http\Response;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Middleware Manager for handling middleware chains
 */
class MiddlewareManager
{
    protected array $globalMiddleware = [];
    protected array $routeMiddleware = [];
    protected array $middlewareAliases = [];
    
    /**
     * Register global middleware
     */
    public function addGlobal(string|MiddlewareInterface $middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }
    
    /**
     * Register middleware alias
     */
    public function alias(string $name, string $class): void
    {
        $this->middlewareAliases[$name] = $class;
    }
    
    /**
     * Set route middleware
     */
    public function setRouteMiddleware(array $middleware): void
    {
        $this->routeMiddleware = $middleware;
    }
    
    /**
     * Process middleware chain
     */
    public function process(Request $request, callable $controller): Response
    {
        $middleware = array_merge($this->globalMiddleware, $this->routeMiddleware);
        
        // Create the middleware chain
        $pipeline = array_reduce(
            array_reverse($middleware),
            function ($next, $middleware) {
                return function (Request $request) use ($middleware, $next) {
                    $middlewareInstance = $this->resolveMiddleware($middleware);
                    return $middlewareInstance->handle($request, $next);
                };
            },
            function (Request $request) use ($controller) {
                $response = new Response();
                
                try {
                    $result = call_user_func($controller, $request, $response);
                    
                    // If controller returns a Response, use it
                    if ($result instanceof Response) {
                        return $result;
                    }
                    
                    // If controller returns data, convert to JSON response
                    if ($result !== null) {
                        return $response->json($result);
                    }
                    
                    return $response;
                } catch (\Exception $e) {
                    return Response::error($e->getMessage(), 500);
                }
            }
        );
        
        return $pipeline($request);
    }
    
    /**
     * Resolve middleware instance
     */
    protected function resolveMiddleware(string|MiddlewareInterface $middleware): MiddlewareInterface
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }
        
        // Check if it's an alias
        if (isset($this->middlewareAliases[$middleware])) {
            $middleware = $this->middlewareAliases[$middleware];
        }
        
        // Create instance
        if (class_exists($middleware)) {
            return new $middleware();
        }
        
        throw new \Exception("Middleware not found: $middleware");
    }
    
    /**
     * Clear route middleware
     */
    public function clearRouteMiddleware(): void
    {
        $this->routeMiddleware = [];
    }
}