<?php 

namespace Core;

use DateTime;

trait Helper
{
    public function sanitizeInput($data)
    {
        return htmlspecialchars(strip_tags($data));
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function FormatDate($date, $format = 'Y-m-d H:i:s')
    {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
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

    public function notFound($message = 'Not Found', $statusCode = 404) {
		$response = ['error' => $message];
		$this->renderJSON($response, $statusCode);
	}

    public function redirect($url, $statusCode = 302) {
		http_response_code($statusCode);
		header("Location: $url");
		exit;
	}

    public function serverError($message = 'Internal Server Error', $statusCode = 500) {
		$response = ['error' => $message];
		$this->renderJSON($response, $statusCode);
	}

    public function setHeader($name, $value) {
		header("$name: $value");
	}

    public function unAuthorized($message = 'Unauthorized', $statusCode = 401) {
		$response = ['error' => $message];
		$this->renderJSON($response, $statusCode);
	}

}
