<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

Trait MainController
{

	public function view($name, $data = [])
	{
		if(!empty($data))
			extract($data);
		
		$filename = "../app/views/".$name.".view.php";
		if(file_exists($filename))
		{
			require $filename;
		}else{

			$filename = "../app/views/404.view.php";
			require $filename;
		}
	}
	public function renderJSON($data = []){
		$json_data = json_encode($data);

		echo "<pre>$json_data</pre>";
	}
}