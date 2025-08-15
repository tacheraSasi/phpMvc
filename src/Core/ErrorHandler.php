<?php

namespace Viper\Core;

use Viper\Http\Response;
use Viper\Logging\Logger;
use Throwable;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Centralized Error Handler
 */
class ErrorHandler
{
    protected bool $debug;
    protected ?Logger $logger;
    protected array $errorFormats = [];
    
    public function __construct(bool $debug = false, ?Logger $logger = null)
    {
        $this->debug = $debug;
        $this->logger = $logger;
        $this->registerDefaultFormats();
    }
    
    /**
     * Register the error handler
     */
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    /**
     * Handle PHP errors
     */
    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }
        
        $exception = new \ErrorException($message, 0, $level, $file, $line);
        $this->handleException($exception);
        
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     */
    public function handleException(Throwable $exception): void
    {
        try {
            $this->logException($exception);
            $response = $this->createErrorResponse($exception);
            $response->send();
        } catch (Throwable $e) {
            // Fallback error handling
            http_response_code(500);
            echo 'Internal Server Error';
        }
        
        exit(1);
    }
    
    /**
     * Handle fatal errors
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            
            $this->handleException($exception);
        }
    }
    
    /**
     * Create error response
     */
    public function createErrorResponse(Throwable $exception): Response
    {
        $statusCode = $this->getStatusCode($exception);
        $format = $this->getResponseFormat();
        
        if (isset($this->errorFormats[$format])) {
            return $this->errorFormats[$format]($exception, $statusCode);
        }
        
        // Default JSON response
        return $this->createJsonResponse($exception, $statusCode);
    }
    
    /**
     * Get HTTP status code from exception
     */
    protected function getStatusCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }
        
        return 500;
    }
    
    /**
     * Get response format
     */
    protected function getResponseFormat(): string
    {
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
        
        if (strpos($acceptHeader, 'application/json') !== false) {
            return 'json';
        }
        
        if (strpos($acceptHeader, 'text/html') !== false) {
            return 'html';
        }
        
        return 'json'; // Default to JSON
    }
    
    /**
     * Register default error formats
     */
    protected function registerDefaultFormats(): void
    {
        $this->errorFormats['json'] = [$this, 'createJsonResponse'];
        $this->errorFormats['html'] = [$this, 'createHtmlResponse'];
    }
    
    /**
     * Create JSON error response
     */
    protected function createJsonResponse(Throwable $exception, int $statusCode): Response
    {
        $data = [
            'error' => true,
            'message' => $exception->getMessage(),
            'status' => $statusCode
        ];
        
        if ($this->debug) {
            $data['debug'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }
        
        return Response::error($data['message'], $statusCode)
                      ->header('Content-Type', 'application/json')
                      ->content(json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Create HTML error response
     */
    protected function createHtmlResponse(Throwable $exception, int $statusCode): Response
    {
        $message = $exception->getMessage();
        
        if ($this->debug) {
            $html = $this->createDebugHtml($exception, $statusCode);
        } else {
            $html = $this->createProductionHtml($message, $statusCode);
        }
        
        $response = new Response();
        return $response->status($statusCode)
                       ->header('Content-Type', 'text/html')
                       ->content($html);
    }
    
    /**
     * Create debug HTML
     */
    protected function createDebugHtml(Throwable $exception, int $statusCode): string
    {
        $message = htmlspecialchars($exception->getMessage());
        $file = htmlspecialchars($exception->getFile());
        $line = $exception->getLine();
        $trace = htmlspecialchars($exception->getTraceAsString());
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Error {$statusCode}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px; }
                .message { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
                .details { background: #f8f9fa; padding: 15px; border-radius: 3px; margin-top: 15px; }
                .trace { background: #e9ecef; padding: 15px; border-radius: 3px; margin-top: 15px; white-space: pre-wrap; }
            </style>
        </head>
        <body>
            <div class='error'>
                <div class='message'>Error {$statusCode}: {$message}</div>
                <div class='details'>
                    <strong>File:</strong> {$file}<br>
                    <strong>Line:</strong> {$line}
                </div>
                <div class='trace'>{$trace}</div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Create production HTML
     */
    protected function createProductionHtml(string $message, int $statusCode): string
    {
        $message = htmlspecialchars($message);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Error {$statusCode}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
                .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 40px; border-radius: 5px; display: inline-block; }
            </style>
        </head>
        <body>
            <div class='error'>
                <h1>Error {$statusCode}</h1>
                <p>{$message}</p>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Log exception
     */
    protected function logException(Throwable $exception): void
    {
        if ($this->logger) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Register custom error format
     */
    public function registerFormat(string $format, callable $handler): void
    {
        $this->errorFormats[$format] = $handler;
    }
}