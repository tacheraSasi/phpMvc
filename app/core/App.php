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
		if (isset($this->routes[$req_method][$URL])) {
            $action = $this->routes[$req_method][$URL];

			dump($action);

			$filename = "../app/controllers/".ucfirst($action[0]).".php";
			if(file_exists($filename))
			{
				require $filename;
				$this->controller = ucfirst($action[0]);
				unset($action[0]);
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
            
        } else {
            echo "404 Not Found";
        }
		

	}	

	public function run(){
		$req = new \Core\Request;
		$this->loadController($req::get_uri(),$req::method());
	}

}


