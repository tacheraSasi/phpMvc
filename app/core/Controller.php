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
	public function renderJSON($data = [], $statusCode = 200){
		http_response_code($statusCode);
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization');
		
		echo json_encode($data, JSON_PRETTY_PRINT);
		exit;
	}
	
	public function sendError($message, $statusCode = 400, $errors = []) {
		$response = ['error' => $message];
		if (!empty($errors)) {
			$response['details'] = $errors;
		}
		$this->renderJSON($response, $statusCode);
	}
	
	public function sendSuccess($data = [], $message = null, $statusCode = 200) {
		$response = ['success' => true];
		if ($message) {
			$response['message'] = $message;
		}
		if (!empty($data)) {
			$response['data'] = $data;
		}
		$this->renderJSON($response, $statusCode);
	}
}