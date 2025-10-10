<?php

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * API Helper Functions
 * Provides convenient functions for API development
 */

if (!function_exists('json_response')) {
    /**
     * Send JSON response
     * @param array $data
     * @param int $status
     * @return void
     */
    function json_response(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}

if (!function_exists('success_response')) {
    /**
     * Send success response
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return void
     */
    function success_response($data = [], string $message = 'Success', int $status = 200): void
    {
        json_response([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}

if (!function_exists('error_response')) {
    /**
     * Send error response
     * @param string $message
     * @param int $status
     * @param array $errors
     * @return void
     */
    function error_response(string $message = 'Error', int $status = 400, array $errors = []): void
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        json_response($response, $status);
    }
}

if (!function_exists('api_response')) {
    /**
     * Generic API response builder
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @return void
     */
    function api_response(bool $success, string $message, $data = null, int $status = 200): void
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        json_response($response, $status);
    }
}

if (!function_exists('paginate_response')) {
    /**
     * Send paginated response
     * @param array $data
     * @param int $total
     * @param int $page
     * @param int $perPage
     * @return void
     */
    function paginate_response(array $data, int $total, int $page = 1, int $perPage = 10): void
    {
        json_response([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $total)
            ]
        ]);
    }
}

if (!function_exists('unauthorized')) {
    /**
     * Send unauthorized response
     * @param string $message
     * @return void
     */
    function unauthorized(string $message = 'Unauthorized'): void
    {
        error_response($message, 401);
    }
}

if (!function_exists('forbidden')) {
    /**
     * Send forbidden response
     * @param string $message
     * @return void
     */
    function forbidden(string $message = 'Forbidden'): void
    {
        error_response($message, 403);
    }
}

if (!function_exists('not_found')) {
    /**
     * Send not found response
     * @param string $message
     * @return void
     */
    function not_found(string $message = 'Not found'): void
    {
        error_response($message, 404);
    }
}

if (!function_exists('validation_error')) {
    /**
     * Send validation error response
     * @param array $errors
     * @param string $message
     * @return void
     */
    function validation_error(array $errors, string $message = 'Validation failed'): void
    {
        error_response($message, 422, $errors);
    }
}

if (!function_exists('cors_headers')) {
    /**
     * Set CORS headers
     * @param array $allowedOrigins
     * @param array $allowedMethods
     * @param array $allowedHeaders
     * @return void
     */
    function cors_headers(
        array $allowedOrigins = ['*'],
        array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        array $allowedHeaders = ['Content-Type', 'Authorization']
    ): void {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        
        header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
        header('Access-Control-Allow-Headers: ' . implode(', ', $allowedHeaders));
        header('Access-Control-Max-Age: 86400');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
