<?php

namespace Core\Middleware;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * JSON Request middleware for handling JSON payloads
 */
class JsonMiddleware
{
    public static function handle()
    {
        // Set JSON content type for API responses by default
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
        }
        
        // Validate JSON input for POST/PUT/PATCH requests
        $method = $_SERVER['REQUEST_METHOD'];
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $input = file_get_contents('php://input');
                if (!empty($input)) {
                    $decoded = json_decode($input, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid JSON format']);
                        exit;
                    }
                }
            }
        }
    }
}