<?php

defined('ROOTPATH') OR exit('Access Denied!');

class App 
{
	private $controller = 'Home';
	private $method 	= 'index';
	protected $routes = [];

	private function splitURL()
	{
		$URL = $_GET['url'] ?? 'home';
		$URL = explode("/", trim($URL,"/"));
		return $URL;	
	}

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

    public function resource($uri, $controller)
    {
        $this->get($uri, [$controller, 'index']);
        $this->post($uri, [$controller, 'store']);
        $this->get($uri . '/{id}', [$controller, 'show']);
        $this->put($uri . '/{id}', [$controller, 'update']);
        $this->delete($uri . '/{id}', [$controller, 'destroy']);
        return $this;
    }

    protected function addRoute($method, $uri, $action)
    {
        $this->routes[$method][$uri] = $action;
        
    }
	public function showRoutes(){
		echo "<pre>";
		var_dump($this->routes);
		echo "</pre>";
	}

	public function loadController($URL,$req_method)
	{
		// Handle OPTIONS requests for CORS
		if ($req_method === 'OPTIONS') {
			$this->handleCors();
			return;
		}

		if (isset($this->routes[$req_method][$URL]) ) {
            $action = $this->routes[$req_method][$URL];
			$controller_name = $action[0];
			if(isset($action[1])){
				#Handling the method to call
				$this->method = $action[1];
			}
			$this->require_controller($controller_name,$this->method);
			
		// Try pattern matching for parameterized routes
		} else {
			$matched = false;
			foreach ($this->routes[$req_method] ?? [] as $pattern => $action) {
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
				require $filename;
				$this->controller = ucfirst($controller_name);
				unset($controller_name);
			}else{

				$filename = "../app/controllers/_404.php";
				require $filename;
				$this->controller = "_404";
			}

			$controller = new ('\Controller\\'.$this->controller);

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


