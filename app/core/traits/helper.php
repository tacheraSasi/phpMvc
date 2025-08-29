<?php

namespace Core;

use DateTime;

/**
 * Trait Helper
 * 
 * Provides utility methods for controllers and services.
 */
trait Helper
{
    /**
     * Render a JSON response and exit.
     * @param array $data
     * @param int $statusCode
     * @return void
     */
    public function renderJSON($data = [], $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Sanitize a string for safe output.
     * @param string $data
     * @return string
     */
    public function sanitizeInput($data)
    {
        return htmlspecialchars(strip_tags($data));
    }

    /**
     * Validate an email address.
     * @param string $email
     * @return bool|string
     */
    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Hash a password using bcrypt.
     * @param string $password
     * @return string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify a password against a hash.
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Format a date string.
     * @param string $date
     * @param string $format
     * @return string
     */
    public function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    }

    /**
     * Send a JSON error response and exit.
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return void
     */
    public function sendError($message, $statusCode = 400, $errors = [])
    {
        $response = ['error' => $message];
        if (!empty($errors)) {
            $response['details'] = $errors;
        }
        $this->renderJSON($response, $statusCode);
    }

    /**
     * Send a JSON success response and exit.
     * @param array $data
     * @param string|null $message
     * @param int $statusCode
     * @return void
     */
    public function sendSuccess($data = [], $message = null, $statusCode = 200)
    {
        $response = ['success' => true];
        if ($message) {
            $response['message'] = $message;
        }
        if (!empty($data)) {
            $response['data'] = $data;
        }
        $this->renderJSON($response, $statusCode);
    }

    /**
     * Send a 404 Not Found JSON response and exit.
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public function notFound($message = 'Not Found', $statusCode = 404)
    {
        $response = ['error' => $message];
        $this->renderJSON($response, $statusCode);
    }

    /**
     * Redirect to a URL and exit.
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    public function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    /**
     * Send a 500 Internal Server Error JSON response and exit.
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public function serverError($message = 'Internal Server Error', $statusCode = 500)
    {
        $response = ['error' => $message];
        $this->renderJSON($response, $statusCode);
    }

    /**
     * Set a custom HTTP header.
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setHeader($name, $value)
    {
        header("$name: $value");
    }

    /**
     * Send a 401 Unauthorized JSON response and exit.
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public function unauthorized($message = 'Unauthorized', $statusCode = 401)
    {
        $response = ['error' => $message];
        $this->renderJSON($response, $statusCode);
    }

    /**
     * Get a value from $_GET, $_POST, or $_REQUEST.
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return $_REQUEST;
        }
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Get a query parameter from $_GET.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getQueryParam($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a POST parameter from $_POST.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPostParam($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get the JSON-decoded request body as an array.
     * @return array|null
     */
    public function getJsonBody()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Check if the request is an AJAX request.
     * @return bool
     */
    public function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Send a file as a response (download or inline).
     * @param string $filePath
     * @param string|null $downloadName
     * @param bool $inline
     * @return void
     */
    public function fileResponse($filePath, $downloadName = null, $inline = false)
    {
        if (!file_exists($filePath)) {
            $this->notFound('File not found');
        }
        $mime = mime_content_type($filePath);
        $name = $downloadName ?: basename($filePath);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $name . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * Send a 204 No Content response and exit.
     * @return void
     */
    public function noContent()
    {
        http_response_code(204);
        exit;
    }

    /**
     * Send a 201 Created response with optional data.
     * @param array $data
     * @param string|null $message
     * @return void
     */
    public function created($data = [], $message = null)
    {
        $this->sendSuccess($data, $message, 201);
    }

    /**
     * Send a 400 Bad Request response.
     * @param string $message
     * @param array $errors
     * @return void
     */
    public function badRequest($message = 'Bad Request', $errors = [])
    {
        $this->sendError($message, 400, $errors);
    }

    /**
     * Send a 403 Forbidden response.
     * @param string $message
     * @return void
     */
    public function forbidden($message = 'Forbidden')
    {
        $this->sendError($message, 403);
    }
}
