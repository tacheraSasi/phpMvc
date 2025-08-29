<?php 

namespace Controller;

use Core\Helper;

defined('ROOTPATH') OR exit('Access Denied!');

Trait MainController
{
	use Helper;

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


	/**
	 * Set multiple headers at once.
	 * @param array $headers
	 * @return void
	 */
	public function setHeaders(array $headers)
	{
		foreach ($headers as $name => $value) {
			header("$name: $value");
		}
	}

	/**
	 * Flash a message to the session (for web apps).
	 * @param string $key
	 * @param string $message
	 * @return void
	 */
	public function flash($key, $message)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		$_SESSION['flash'][$key] = $message;
	}

	/**
	 * Get and clear a flashed message from the session.
	 * @param string $key
	 * @return string|null
	 */
	public function getFlash($key)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		if (isset($_SESSION['flash'][$key])) {
			$msg = $_SESSION['flash'][$key];
			unset($_SESSION['flash'][$key]);
			return $msg;
		}
		return null;
	}

	/**
	 * Check if the request method matches.
	 * @param string $method
	 * @return bool
	 */
	public function isMethod($method)
	{
		return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === strtoupper($method);
	}

	/**
	 * Get all request input (GET, POST, JSON).
	 * @return array
	 */
	public function allInput()
	{
		$input = $_REQUEST;
		$json = json_decode(file_get_contents('php://input'), true);
		if (is_array($json)) {
			$input = array_merge($input, $json);
		}
		return $input;
	}

	/**
	 * Abort the request with a given status code and message.
	 * @param int $statusCode
	 * @param string $message
	 * @return void
	 */
	public function abort($statusCode = 500, $message = 'Server Error')
	{
		http_response_code($statusCode);
		exit($message);
	}

	/**
	 * Download a file.
	 * @param string $filePath
	 * @param string|null $downloadName
	 * @return void
	 */
	public function download($filePath, $downloadName = null)
	{
		if (!file_exists($filePath)) {
			$this->notFound('File not found');
		}
		$name = $downloadName ?: basename($filePath);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filePath));
		readfile($filePath);
		exit;
	}

	/**
	 * Return a JSON response without exiting.
	 * @param array $data
	 * @param int $statusCode
	 * @return void
	 */
	public function jsonResponse($data = [], $statusCode = 200)
	{
		http_response_code($statusCode);
		header('Content-Type: application/json');
		echo json_encode($data, JSON_PRETTY_PRINT);
	}

	/**
	 * Validate input data with rules (simple version).
	 * @param array $data
	 * @param array $rules
	 * @return array [bool $valid, array $errors]
	 */
	public function validate(array $data, array $rules)
	{
		$errors = [];
		foreach ($rules as $field => $rule) {
			$parts = explode('|', $rule);
			foreach ($parts as $r) {
				if ($r === 'required' && empty($data[$field])) {
					$errors[$field][] = 'The ' . $field . ' field is required.';
				}
				if ($r === 'email' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
					$errors[$field][] = 'The ' . $field . ' must be a valid email address.';
				}
				if (strpos($r, 'min:') === 0) {
					$min = (int)substr($r, 4);
					if (isset($data[$field]) && strlen($data[$field]) < $min) {
						$errors[$field][] = 'The ' . $field . ' must be at least ' . $min . ' characters.';
					}
				}
				if (strpos($r, 'max:') === 0) {
					$max = (int)substr($r, 4);
					if (isset($data[$field]) && strlen($data[$field]) > $max) {
						$errors[$field][] = 'The ' . $field . ' must be at most ' . $max . ' characters.';
					}
				}
			}
		}
		return [empty($errors), $errors];
	}

	/**
	 * Check if the user is authenticated (simple session check).
	 * @return bool
	 */
	public function isAuthenticated()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		return !empty($_SESSION['user']);
	}

	/**
	 * Get the current authenticated user from session.
	 * @return mixed|null
	 */
	public function currentUser()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		return $_SESSION['user'] ?? null;
	}

	/**
	 * Log out the current user (destroy session).
	 * @return void
	 */
	public function logout()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		session_destroy();
	}

	/**
	 * Generate a CSRF token and store in session.
	 * @return string
	 */
	public function csrfToken()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		if (empty($_SESSION['csrf_token'])) {
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
		return $_SESSION['csrf_token'];
	}

	/**
	 * Verify a CSRF token from request.
	 * @param string $token
	 * @return bool
	 */
	public function verifyCsrfToken($token)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
	}
}