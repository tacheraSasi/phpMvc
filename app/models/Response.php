<?php 

/**
 * Response class
 * Handles HTTP responses
 */

namespace Luna;

defined('ROOTPATH') OR exit('Access Denied!');

class Response
{
    private $statusCode = 200;
    private $headers = [];
    
    /**
     * Set HTTP status code
     * @param int $code
     * @return Response
     */
    public function status(int $code): Response
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set a response header
     * @param string $name
     * @param string $value
     * @return Response
     */
    public function header(string $name, string $value): Response
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Send JSON response
     * @param array|object $data
     * @param int|null $statusCode
     * @return void
     */
    public function json($data, ?int $statusCode = null): void
    {
        if ($statusCode !== null) {
            $this->statusCode = $statusCode;
        }
        
        http_response_code($this->statusCode);
        
        // Set default JSON header
        header('Content-Type: application/json');
        
        // Set custom headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Send text response
     * @param string $text
     * @param int|null $statusCode
     * @return void
     */
    public function send(string $text, ?int $statusCode = null): void
    {
        if ($statusCode !== null) {
            $this->statusCode = $statusCode;
        }
        
        http_response_code($this->statusCode);
        
        // Set custom headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        echo $text;
        exit;
    }

    /**
     * Redirect to a URL
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }
}
