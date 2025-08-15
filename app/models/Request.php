<?php 

/**
 * Request class
 * Gets and sets data in the POST and GET global variables
 */

namespace Luna;

defined('ROOTPATH') OR exit('Access Denied!');

class Request
{
	
	/** checks which post method was used **/
	public static function method():string
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	public static function get_uri(){
		return $URL = $_GET['url'] ?? 'home';
		#return trim(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),'/');
	}

	/** checks if something was posted **/
	public function posted():bool 
	{
		if($_SERVER['REQUEST_METHOD'] == "POST" && count($_POST) > 0)
		{
			return true;
		}

		return false;
	}


	/** get a value from the POST variable **/
	public function post(string $key = '', mixed $default = ''):mixed
	{

		if(empty($key))
		{
			return $_POST;
		}else
		if(isset($_POST[$key]))
		{
			return $_POST[$key];
		}

		return $default;
	}

	/** get a value from the FILES variable **/
	public function files(string $key = '', mixed $default = ''):mixed
	{

		if(empty($key))
		{
			return $_FILES;
		}else
		if(isset($_FILES[$key]))
		{
			return $_FILES[$key];
		}

		return $default;
	}

	/** get a value from the GET variable **/
	public function get(string $key = '', mixed $default = ''):mixed
	{

		if(empty($key))
		{
			return $_GET;
		}else
		if(isset($_GET[$key]))
		{
			return $_GET[$key];
		}

		return $default;
	}

	/** get input from either POST or JSON based on content type **/
	public function input(string $key, mixed $default = ''):mixed
	{
		if ($this->isJson()) {
			return $this->json($key, $default);
		}
		
		if(isset($_REQUEST[$key])) {
			return $_REQUEST[$key];
		}

		return $default;
	}

	/** get all values from the REQUEST variable **/
	public function all():mixed
	{
		return $_REQUEST;
	}

	/** get JSON input from request body **/
	public function json(string $key = '', mixed $default = ''):mixed
	{
		$input = file_get_contents('php://input');
		$data = json_decode($input, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			return $default;
		}
		
		if(empty($key)) {
			return $data ?? [];
		}
		
		return $data[$key] ?? $default;
	}

	/** get content type of request **/
	public static function contentType():string
	{
		return $_SERVER['CONTENT_TYPE'] ?? '';
	}

	/** check if request is JSON **/
	public function isJson():bool
	{
		return strpos($this->contentType(), 'application/json') !== false;
	}

	/** get all input from either POST or JSON **/
	public function allInput():mixed
	{
		if ($this->isJson()) {
			return $this->json();
		}
		
		return $_REQUEST;
	}
}