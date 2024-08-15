<?php
defined('ROOTPATH') OR exit('Access Denied!');


namespace Core;

class Router
{
    protected $routes = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    protected function addRoute($method, $uri, $action)
    {
        $this->routes[$method][$uri] = $action;
        
    }

    public function dispatch($uri, $method)
    {
        $uri = trim($uri, '/');
        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];

            if (is_callable($action)) {
                call_user_func($action);
            } elseif (is_array($action)) {
                $this->callControllerAction($action);
            }
        } else {
            echo "404 Not Found";
        }
    }

    protected function callControllerAction($action)
    {
        list($controller, $method) = $action;

        if (class_exists($controller)) {
            $controller = new $controller();
            if (method_exists($controller, $method)) {
                call_user_func([$controller, $method]);
            } else {
                echo "Method {$method} not found in controller {$controller}";
            }
        } else {
            echo "Controller {$controller} not found";
        }
    }
}