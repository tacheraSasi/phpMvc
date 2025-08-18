<?php

defined('ROOTPATH') OR exit('Access Denied!');

class App 
{
	private $controller = 'Home';
	private $method 	= 'index';
	protected static $routes = [];
	protected $instanceRoutes = [];

	private function splitURL()
	{
		$URL = $_GET['url'] ?? 'home';
		$URL = explode("/", trim($URL,"/"));
		return $URL;	
	}

	// Static route registration
	public static function get($uri, $action)
	{
		self::addRoute('GET', $uri, $action);
		return new static;
	}

	public static function post($uri, $action)
	{
		self::addRoute('POST', $uri, $action);
		return new static;
	}

	public static function put($uri, $action)
	{
		self::addRoute('PUT', $uri, $action);
		return new static;
	}

	public static function delete($uri, $action)
	{
		self::addRoute('DELETE', $uri, $action);
		return new static;
	}

	public static function patch($uri, $action)
	{
		self::addRoute('PATCH', $uri, $action);
		return new static;
	}

	public static function options($uri, $action)
	{
		self::addRoute('OPTIONS', $uri, $action);
		return new static;
	}

	public static function resource($uri, $controller)
	{
		self::get($uri, [$controller, 'index']);
		self::post($uri, [$controller, 'store']);
		self::get($uri . '/{id}', [$controller, 'show']);
		self::put($uri . '/{id}', [$controller, 'update']);
		self::delete($uri . '/{id}', [$controller, 'destroy']);
		return new static;
	}

	// Instance route registration
	public function iget($uri, $action)
	{
		$this->addInstanceRoute('GET', $uri, $action);
		return $this;
	}
	public function ipost($uri, $action)
	{
		$this->addInstanceRoute('POST', $uri, $action);
		return $this;
	}
	public function iput($uri, $action)
	{
		$this->addInstanceRoute('PUT', $uri, $action);
		return $this;
	}
	public function idelete($uri, $action)
	{
		$this->addInstanceRoute('DELETE', $uri, $action);
		return $this;
	}
	public function ipatch($uri, $action)
	{
		$this->addInstanceRoute('PATCH', $uri, $action);
		return $this;
	}
	public function ioptions($uri, $action)
	{
		$this->addInstanceRoute('OPTIONS', $uri, $action);
		return $this;
	}
	public function iresource($uri, $controller)
	{
		$this->iget($uri, [$controller, 'index']);
		$this->ipost($uri, [$controller, 'store']);
		$this->iget($uri . '/{id}', [$controller, 'show']);
		$this->iput($uri . '/{id}', [$controller, 'update']);
		$this->idelete($uri . '/{id}', [$controller, 'destroy']);
		return $this;
	}

	protected static function addRoute($method, $uri, $action)
	{
		self::$routes[$method][$uri] = $action;
	}
	protected function addInstanceRoute($method, $uri, $action)
	{
		$this->instanceRoutes[$method][$uri] = $action;
	}
	public function showRoutes(){
		echo "<pre>";
		echo "Static routes:\n";
		var_dump(self::$routes);
		echo "Instance routes:\n";
		var_dump($this->instanceRoutes);
		echo "</pre>";
	}

	public function loadController($URL,$req_method)
	{
		// Handle OPTIONS requests for CORS
		if ($req_method === 'OPTIONS') {
			$this->handleCors();
			return;
		}

		// Check instance routes first
		if (isset($this->instanceRoutes[$req_method][$URL])) {
			$action = $this->instanceRoutes[$req_method][$URL];
			$controller_name = $action[0];
			if(isset($action[1])){
				$this->method = $action[1];
			}
			$this->require_controller($controller_name,$this->method);
			return;
		}

		// Then check static routes
		if (isset(self::$routes[$req_method][$URL]) ) {
			$action = self::$routes[$req_method][$URL];
			$controller_name = $action[0];
			if(isset($action[1])){
				$this->method = $action[1];
			}
			$this->require_controller($controller_name,$this->method);
            
		// Try pattern matching for parameterized routes
		} else {
			$matched = false;
			// Check instance pattern routes
			foreach ($this->instanceRoutes[$req_method] ?? [] as $pattern => $action) {
				$params = $this->matchRoute($pattern, $URL);
				if ($params !== false) {
					$controller_name = $action[0];
					if(isset($action[1])){
						$this->method = $action[1];
					}
					$this->require_controller($controller_name, $this->method, $params);
					$matched = true;
					break;
				}
			}
			// Check static pattern routes
			if (!$matched) {
				foreach (self::$routes[$req_method] ?? [] as $pattern => $action) {
					$params = $this->matchRoute($pattern, $URL);
					if ($params !== false) {
						$controller_name = $action[0];
						if(isset($action[1])){
							$this->method = $action[1];
						}
						$this->require_controller($controller_name, $this->method, $params);
						$matched = true;
						break;
					}
				}
			}
			if (!$matched) {
				if($URL === 'home'){
					$this->require_controller('home');
				} else {
					$this->require_controller('_404');
				}
			}
		}
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

	protected function handleCors()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		header('Access-Control-Max-Age: 86400');
		http_response_code(200);
		exit;
	}
	public function require_controller($controller_name, $method_name='index', $params = []){
		$filename = "../app/controllers/".$controller_name.".php";
		if(file_exists($filename))
		{
			require_once $filename;
			$this->controller = ucfirst($controller_name);
		}else{
			$filename = "../app/controllers/_404.php";
			require_once $filename;
			$this->controller = "_404";
		}

		$controller_class = '\\Controller\\' . $this->controller;
		if(class_exists($controller_class)) {
			$controller = new $controller_class();
		} else if(class_exists($this->controller)) {
			$controller = new $this->controller();
		} else {
			// fallback: try without namespace
			$controller = new $this->controller();
		}

		if(!empty($method_name))
		{
			if(method_exists($controller, $method_name))
			{
				$this->method = $method_name;
			} 
		}

		call_user_func_array([$controller,$this->method], $params);
	}

	public function run(){
		$req = new \Luna\Request;
		$this->loadController($req::get_uri(),$req::method());
	}

}


