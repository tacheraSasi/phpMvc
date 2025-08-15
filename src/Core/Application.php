<?php

namespace Viper\Core;

use Viper\Http\Request;
use Viper\Http\Response;
use Viper\Middleware\CorsMiddleware;
use Viper\Middleware\RateLimitMiddleware;
use Viper\Logging\Logger;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Main Viper Application class
 */
class Application
{
    protected Router $router;
    protected Container $container;
    protected ?Logger $logger = null;
    protected bool $booted = false;
    
    public function __construct()
    {
        $this->container = Container::getInstance();
        $this->router = new Router();
        
        $this->registerCoreServices();
        $this->registerMiddleware();
    }
    
    /**
     * Register core services
     */
    protected function registerCoreServices(): void
    {
        // Bind core services to container
        $this->container->singleton(Router::class, fn() => $this->router);
        $this->container->singleton(Logger::class, fn() => new Logger());
        $this->container->singleton(Request::class, fn() => new Request());
        $this->container->singleton(Response::class, fn() => new Response());
    }
    
    /**
     * Register default middleware
     */
    protected function registerMiddleware(): void
    {
        // Register middleware aliases
        $this->router->aliasMiddleware('cors', CorsMiddleware::class);
        $this->router->aliasMiddleware('rate-limit', RateLimitMiddleware::class);
        
        // Add global CORS middleware if enabled
        if (config('CORS_ENABLED', true)) {
            $this->router->middleware('cors');
        }
        
        // Add rate limiting if enabled
        if (config('RATE_LIMIT_ENABLED', true)) {
            $maxRequests = (int) config('RATE_LIMIT_REQUESTS', 60);
            $window = (int) config('RATE_LIMIT_WINDOW', 60);
            $this->router->getMiddlewareManager()->addGlobal(new RateLimitMiddleware($maxRequests, $window));
        }
    }
    
    /**
     * Boot the application
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }
        
        // Load configuration
        Config::loadEnv();
        
        // Set up error handling
        $this->setupErrorHandling();
        
        // Set up logging
        $this->setupLogging();
        
        $this->booted = true;
    }
    
    /**
     * Set up error handling
     */
    protected function setupErrorHandling(): void
    {
        $debug = Config::isDebug();
        $logger = $this->container->resolve(Logger::class);
        
        $errorHandler = new ErrorHandler($debug, $logger);
        $errorHandler->register();
    }
    
    /**
     * Set up logging
     */
    protected function setupLogging(): void
    {
        $this->logger = $this->container->resolve(Logger::class);
        $minLevel = config('LOG_LEVEL', 'debug');
        $this->logger->setMinLevel($minLevel);
    }
    
    /**
     * Add GET route
     */
    public function get(string $uri, $action): Route
    {
        return $this->router->get($uri, $action);
    }
    
    /**
     * Add POST route
     */
    public function post(string $uri, $action): Route
    {
        return $this->router->post($uri, $action);
    }
    
    /**
     * Add PUT route
     */
    public function put(string $uri, $action): Route
    {
        return $this->router->put($uri, $action);
    }
    
    /**
     * Add DELETE route
     */
    public function delete(string $uri, $action): Route
    {
        return $this->router->delete($uri, $action);
    }
    
    /**
     * Add middleware
     */
    public function middleware(string|array $middleware): self
    {
        $this->router->middleware($middleware);
        return $this;
    }
    
    /**
     * Run the application
     */
    public function run(): void
    {
        $this->boot();
        
        $startTime = microtime(true);
        $request = $this->container->resolve(Request::class);
        
        // Log request
        if ($this->logger) {
            $this->logger->info('Request started', [
                'method' => $request->method(),
                'uri' => $request->uri(),
                'ip' => $request->ip()
            ]);
        }
        
        try {
            $response = $this->router->dispatch($request);
            $response->send();
            
            // Log response
            if ($this->logger) {
                $executionTime = round((microtime(true) - $startTime) * 1000, 2);
                $this->logger->info('Request completed', [
                    'status' => $response->getStatusCode(),
                    'execution_time_ms' => $executionTime
                ]);
            }
        } catch (\Exception $e) {
            // Error logging is handled by ErrorHandler
            $response = Response::error($e->getMessage(), 500);
            $response->send();
        }
    }
    
    /**
     * Get the router instance
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
    
    /**
     * Get the container instance
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
    
    /**
     * Get the logger instance
     */
    public function getLogger(): ?Logger
    {
        return $this->logger;
    }
}