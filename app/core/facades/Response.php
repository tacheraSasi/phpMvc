<?php

namespace Core\Facades;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Response Facade - Provides static access to HTTP response helpers
 * 
 * Usage:
 * Response::json(['data' => $data]);
 * Response::success($data, 'Success message');
 * Response::error('Error message', 400);
 */
class Response extends Facade
{
    /**
     * Get the accessor name for the facade
     * @return string
     */
    protected static function getAccessor(): string
    {
        return 'Response';
    }

    /**
     * Send JSON response
     * @param array $data
     * @param int $status
     * @return void
     */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Send success response
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return void
     */
    public static function success($data = [], string $message = 'Success', int $status = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Send error response
     * @param string $message
     * @param int $status
     * @param array $errors
     * @return void
     */
    public static function error(string $message = 'Error', int $status = 400, array $errors = []): void
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        self::json($response, $status);
    }

    /**
     * Send created response
     * @param mixed $data
     * @param string $message
     * @return void
     */
    public static function created($data = [], string $message = 'Created successfully'): void
    {
        self::success($data, $message, 201);
    }

    /**
     * Send no content response
     * @return void
     */
    public static function noContent(): void
    {
        http_response_code(204);
        exit;
    }

    /**
     * Send unauthorized response
     * @param string $message
     * @return void
     */
    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, 401);
    }

    /**
     * Send forbidden response
     * @param string $message
     * @return void
     */
    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error($message, 403);
    }

    /**
     * Send not found response
     * @param string $message
     * @return void
     */
    public static function notFound(string $message = 'Not found'): void
    {
        self::error($message, 404);
    }

    /**
     * Send validation error response
     * @param array $errors
     * @param string $message
     * @return void
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): void
    {
        self::error($message, 422, $errors);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @param int $status
     * @return void
     */
    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    /**
     * Send download response
     * @param string $file
     * @param string|null $name
     * @return void
     */
    public static function download(string $file, ?string $name = null): void
    {
        if (!file_exists($file)) {
            self::notFound('File not found');
        }

        $name = $name ?? basename($file);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}
