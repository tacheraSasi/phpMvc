<?php

defined('ROOTPATH') or exit('Access Denied!');

/**
 * Global error handler for API responses
 */
class ErrorHandler
{
    public static function register()
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return;
        }

        $error = [
            'error' => 'Internal Server Error',
            'message' => $message,
            'file' => $file,
            'line' => $line
        ];

        if (DEBUG) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode($error, JSON_PRETTY_PRINT);
        } else {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal Server Error']);
        }
        exit;
    }

    public static function handleException($exception)
    {
        $error = [
            'error' => 'Internal Server Error',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ];

        if (DEBUG) {
            $error['trace'] = $exception->getTraceAsString();
        }

        http_response_code(500);
        header('Content-Type: application/json');

        if (DEBUG) {
            echo json_encode($error, JSON_PRETTY_PRINT);
        } else {
            echo json_encode(['error' => 'Internal Server Error']);
        }
        exit;
    }

    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}
