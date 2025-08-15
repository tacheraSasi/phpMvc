<?php

namespace Viper\Middleware;

use Viper\Http\Request;
use Viper\Http\Response;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * CORS Middleware
 */
class CorsMiddleware implements MiddlewareInterface
{
    protected array $allowedOrigins = ['*'];
    protected array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
    protected array $allowedHeaders = ['Content-Type', 'Authorization', 'X-Requested-With'];
    
    public function __construct(array $config = [])
    {
        if (isset($config['origins'])) {
            $this->allowedOrigins = $config['origins'];
        }
        if (isset($config['methods'])) {
            $this->allowedMethods = $config['methods'];
        }
        if (isset($config['headers'])) {
            $this->allowedHeaders = $config['headers'];
        }
    }
    
    public function handle(Request $request, callable $next): Response
    {
        // Handle preflight OPTIONS request
        if ($request->method() === 'OPTIONS') {
            $response = new Response();
            return $this->addCorsHeaders($response);
        }
        
        $response = $next($request);
        return $this->addCorsHeaders($response);
    }
    
    protected function addCorsHeaders(Response $response): Response
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        if (in_array('*', $this->allowedOrigins) || in_array($origin, $this->allowedOrigins)) {
            $response->header('Access-Control-Allow-Origin', in_array('*', $this->allowedOrigins) ? '*' : $origin);
        }
        
        $response->header('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods))
                 ->header('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders))
                 ->header('Access-Control-Max-Age', '3600');
        
        return $response;
    }
}