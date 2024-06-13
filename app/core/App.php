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
		var_dump($URL);
		return $URL;	
	}

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
	public function showRoutes(){
		echo "<pre>";
		var_dump($this->routes);
		echo "</pre>";
	}

	public function loadController($URL,$req_method)
	{
		dump($URL);
		if (isset($this->routes[$req_method][$URL]) ) {
            $action = $this->routes[$req_method][$URL];
			dump($action);
			$controller_name = $action[0];
			if(isset($action[1])){
				#Handling the method to call
				$this->method = $action[1];
			}
			$this->require_controller($controller_name,$this->method);
			
        }elseif($URL === 'home'){
			$this->require_controller('home');
		} else {
			$this->require_controller('_404');
        }
		

	}	
	public function require_controller($controller_name, $method_name='index'){
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

			if(!empty($action[1]))
			{
				if(method_exists($controller, $action[1]))
				{
					$this->method = $action[1];
					unset($action[1]);
				}	
			}

			call_user_func_array([$controller,$this->method], []);
	}

	public function run(){
		$req = new \Core\Request;
		$this->loadController($req::get_uri(),$req::method());
	}

}


