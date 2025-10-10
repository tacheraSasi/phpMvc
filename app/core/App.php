<?php

defined('ROOTPATH') OR exit('Access Denied!');

class App 
{
	private $controller = 'Home';
	private $method 	= 'index';
	protected static $routes = [];
	protected $instanceRoutes = [];
	private static $currentInstance = null;

	private function splitURL()
	{
		$URL = $_GET['url'] ?? 'home';
		$URL = explode("/", trim($URL,"/"));
		return $URL;	
	}

	// Route registration methods (work for both static and instance calls)
	public static function get($uri, $action)
	{
		if (self::$currentInstance !== null) {
			self::$currentInstance->addInstanceRoute('GET', $uri, $action);
			return self::$currentInstance;
		}
		self::addRoute('GET', $uri, $action);
		return new static;
	}

	public static function post($uri, $action)
	{
		if (self::$currentInstance !== null) {
			self::$currentInstance->addInstanceRoute('POST', $uri, $action);
			return self::$currentInstance;
		}
		self::addRoute('POST', $uri, $action);
		return new static;
	}

	public static function put($uri, $action)
	{
		if (self::$currentInstance !== null) {
			self::$currentInstance->addInstanceRoute('PUT', $uri, $action);
			return self::$currentInstance;
		}
		self::addRoute('PUT', $uri, $action);
		return new static;
	}

	public static function delete($uri, $action)
	{
		if (self::$currentInstance !== null) {
			self::$currentInstance->addInstanceRoute('DELETE', $uri, $action);
			return self::$currentInstance;
		}
		self::addRoute('DELETE', $uri, $action);
		return new static;
	}

	public static function patch($uri, $action)
	{
		if (self::$currentInstance !== null) {
			self::$currentInstance->addInstanceRoute('PATCH', $uri, $action);
			return self::$currentInstance;
		}
		self::addRoute('PATCH', $uri, $action);
		return new static;
	}

	public static function options($uri, $action)
	{
		if (self::$currentInstance !== null) {
			self::$currentInstance->addInstanceRoute('OPTIONS', $uri, $action);
			return self::$currentInstance;
		}
		self::addRoute('OPTIONS', $uri, $action);
		return new static;
	}

	public static function resource($uri, $controller)
	{
		if (self::$currentInstance !== null) {
			self::$currentInstance->get($uri, [$controller, 'index']);
			self::$currentInstance->post($uri, [$controller, 'store']);
			self::$currentInstance->get($uri . '/{id}', [$controller, 'show']);
			self::$currentInstance->put($uri . '/{id}', [$controller, 'update']);
			self::$currentInstance->delete($uri . '/{id}', [$controller, 'destroy']);
			return self::$currentInstance;
		}
		self::get($uri, [$controller, 'index']);
		self::post($uri, [$controller, 'store']);
		self::get($uri . '/{id}', [$controller, 'show']);
		self::put($uri . '/{id}', [$controller, 'update']);
		self::delete($uri . '/{id}', [$controller, 'destroy']);
		return new static;
	}
	
	public function __call($name, $arguments)
	{
		// When called as instance method, set current instance and call static method
		$validMethods = ['get', 'post', 'put', 'delete', 'patch', 'options', 'resource'];
		if (in_array($name, $validMethods)) {
			self::$currentInstance = $this;
			$result = call_user_func_array([self::class, $name], $arguments);
			self::$currentInstance = null;
			return $result;
		}
		throw new \Exception("Method {$name} not found");
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
		// Normalize URI: '/' becomes 'home' (default route)
		$uri = trim($uri, '/');
		if ($uri === '' || $uri === '/') {
			$uri = 'home';
		}
		self::$routes[$method][$uri] = $action;
	}
	protected function addInstanceRoute($method, $uri, $action)
	{
		// Normalize URI: '/' becomes 'home' (default route)
		$uri = trim($uri, '/');
		if ($uri === '' || $uri === '/') {
			$uri = 'home';
		}
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
		// Normalize URL: empty string or '/' becomes 'home'
		$URL = trim($URL, '/');
		if ($URL === '') {
			$URL = 'home';
		}
		
		// Handle OPTIONS requests for CORS
		if ($req_method === 'OPTIONS') {
			$this->handleCors();
			return;
		}

		// Check instance routes first
		if (isset($this->instanceRoutes[$req_method][$URL])) {
			$action = $this->instanceRoutes[$req_method][$URL];
			
			// Handle closure
			if (is_callable($action)) {
				$this->executeAction($action, []);
				return;
			}
			
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
			
			// Handle closure
			if (is_callable($action)) {
				$this->executeAction($action, []);
				return;
			}
			
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
					// Handle closure
					if (is_callable($action)) {
						$this->executeAction($action, $params);
						$matched = true;
						break;
					}
					
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
						// Handle closure
						if (is_callable($action)) {
							$this->executeAction($action, $params);
							$matched = true;
							break;
						}
						
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

	protected function executeAction($action, $params = [])
	{
		if (is_callable($action)) {
			// For closures, pass Request and Response objects
			$req = new \Luna\Request();
			$res = new \Luna\Response();
			call_user_func($action, $req, $res);
		}
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


