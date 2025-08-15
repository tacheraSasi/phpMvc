<?php

namespace Core;

defined('ROOTPATH') OR exit('Access Denied!');

class Router
{
    protected $routes = [];
    protected $middlewares = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
        return $this;
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
        return $this;
    }

    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
        return $this;
    }

    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
        return $this;
    }

    public function patch($uri, $action)
    {
        $this->addRoute('PATCH', $uri, $action);
        return $this;
    }

    public function options($uri, $action)
    {
        $this->addRoute('OPTIONS', $uri, $action);
        return $this;
    }

    protected function addRoute($method, $uri, $action)
    {
        $this->routes[$method][$uri] = $action;
    }

    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function dispatch($uri, $method)
    {
        $uri = trim($uri, '/');
        
        // Handle OPTIONS requests for CORS
        if ($method === 'OPTIONS') {
            $this->handleCors();
            return;
        }

        // Execute global middlewares
        foreach ($this->middlewares as $middleware) {
            if (is_callable($middleware)) {
                call_user_func($middleware);
            }
        }

        // Try exact match first
        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];
            $this->executeAction($action, []);
            return;
        }

        // Try pattern matching for parameterized routes
        foreach ($this->routes[$method] ?? [] as $pattern => $action) {
            $params = $this->matchRoute($pattern, $uri);
            if ($params !== false) {
                $this->executeAction($action, $params);
                return;
            }
        }

        // No route found
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found']);
    }

    protected function matchRoute($pattern, $uri)
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            return $matches;
        }

        return false;
    }

    protected function executeAction($action, $params = [])
    {
        if (is_callable($action)) {
            call_user_func_array($action, $params);
        } elseif (is_array($action)) {
            $this->callControllerAction($action, $params);
        }
    }

    protected function callControllerAction($action, $params = [])
    {
        if (count($action) < 2) {
            $action[] = 'index'; // Default method
        }
        
        list($controller, $method) = $action;

        if (class_exists($controller)) {
            $controller = new $controller();
            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], $params);
            } else {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => "Method {$method} not found in controller"]);
            }
        } else {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => "Controller {$controller} not found"]);
        }
    }

    protected function handleCors()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
        http_response_code(200);
    }

    public function resource($uri, $controller)
    {
        $this->get($uri, [$controller, 'index']);
        $this->post($uri, [$controller, 'store']);
        $this->get($uri . '/{id}', [$controller, 'show']);
        $this->put($uri . '/{id}', [$controller, 'update']);
        $this->delete($uri . '/{id}', [$controller, 'destroy']);
        return $this;
    }
}