<?php

namespace Viper\Http;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Enhanced Response class with helpers for JSON, text, files, and status codes
 */
class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $content = '';
    protected bool $sent = false;
    
    protected static array $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
    ];
    
    /**
     * Set response status code
     */
    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }
    
    /**
     * Set response header
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * Set multiple headers
     */
    public function headers(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }
        return $this;
    }
    
    /**
     * Send JSON response
     */
    public function json(mixed $data, int $status = 200): self
    {
        return $this->status($status)
                    ->header('Content-Type', 'application/json')
                    ->content(json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Send text response
     */
    public function text(string $text, int $status = 200): self
    {
        return $this->status($status)
                    ->header('Content-Type', 'text/plain')
                    ->content($text);
    }
    
    /**
     * Send HTML response
     */
    public function html(string $html, int $status = 200): self
    {
        return $this->status($status)
                    ->header('Content-Type', 'text/html')
                    ->content($html);
    }
    
    /**
     * Send file download response
     */
    public function download(string $filePath, string $filename = null): self
    {
        if (!file_exists($filePath)) {
            return $this->status(404)->text('File not found');
        }
        
        $filename = $filename ?? basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        
        return $this->status(200)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->header('Content-Length', (string) filesize($filePath))
                    ->content(file_get_contents($filePath));
    }
    
    /**
     * Send redirect response
     */
    public function redirect(string $url, int $status = 302): self
    {
        return $this->status($status)
                    ->header('Location', $url);
    }
    
    /**
     * Set response content
     */
    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Set cookie
     */
    public function cookie(
        string $name, 
        string $value, 
        int $expire = 0, 
        string $path = '/', 
        string $domain = '', 
        bool $secure = false, 
        bool $httponly = true
    ): self {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        return $this;
    }
    
    /**
     * Send the response
     */
    public function send(): void
    {
        if ($this->sent) {
            return;
        }
        
        // Send status
        http_response_code($this->statusCode);
        
        // Send headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        // Send content
        echo $this->content;
        
        $this->sent = true;
    }
    
    /**
     * Get status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * Get header value
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }
    
    /**
     * Get all headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    /**
     * Get content
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    /**
     * Create error response
     */
    public static function error(string $message, int $status = 500): self
    {
        $response = new self();
        return $response->json([
            'error' => true,
            'message' => $message,
            'status' => $status
        ], $status);
    }
    
    /**
     * Create success response
     */
    public static function success(mixed $data = null, string $message = 'Success'): self
    {
        $response = new self();
        return $response->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
}