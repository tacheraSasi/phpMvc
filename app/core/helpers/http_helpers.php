<?php

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * HTTP Helper Functions
 * Provides convenient HTTP-related functions
 */

if (!function_exists('request_method')) {
    /**
     * Get current request method
     * @return string
     */
    function request_method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}

if (!function_exists('is_get')) {
    /**
     * Check if request is GET
     * @return bool
     */
    function is_get(): bool
    {
        return request_method() === 'GET';
    }
}

if (!function_exists('is_post')) {
    /**
     * Check if request is POST
     * @return bool
     */
    function is_post(): bool
    {
        return request_method() === 'POST';
    }
}

if (!function_exists('is_ajax')) {
    /**
     * Check if request is AJAX
     * @return bool
     */
    function is_ajax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

if (!function_exists('is_json_request')) {
    /**
     * Check if request content type is JSON
     * @return bool
     */
    function is_json_request(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($contentType, 'application/json');
    }
}

if (!function_exists('get_json_input')) {
    /**
     * Get JSON input as array
     * @return array|null
     */
    function get_json_input(): ?array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
}

if (!function_exists('get_header')) {
    /**
     * Get request header
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_header(string $key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('get_bearer_token')) {
    /**
     * Get bearer token from Authorization header
     * @return string|null
     */
    function get_bearer_token(): ?string
    {
        $authorization = get_header('Authorization');
        
        if ($authorization && str_starts_with($authorization, 'Bearer ')) {
            return substr($authorization, 7);
        }
        
        return null;
    }
}

if (!function_exists('client_ip')) {
    /**
     * Get client IP address
     * @return string
     */
    function client_ip(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

if (!function_exists('request_uri')) {
    /**
     * Get request URI
     * @return string
     */
    function request_uri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}

if (!function_exists('current_url')) {
    /**
     * Get current full URL
     * @return string
     */
    function current_url(): string
    {
        $protocol = is_https() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . request_uri();
    }
}

if (!function_exists('is_https')) {
    /**
     * Check if request is HTTPS
     * @return bool
     */
    function is_https(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }
}

if (!function_exists('user_agent')) {
    /**
     * Get user agent
     * @return string
     */
    function user_agent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}

if (!function_exists('set_http_header')) {
    /**
     * Set HTTP header
     * @param string $name
     * @param string $value
     * @return void
     */
    function set_http_header(string $name, string $value): void
    {
        header("$name: $value");
    }
}

if (!function_exists('set_status_code')) {
    /**
     * Set HTTP status code
     * @param int $code
     * @return void
     */
    function set_status_code(int $code): void
    {
        http_response_code($code);
    }
}

if (!function_exists('download_file')) {
    /**
     * Force download a file
     * @param string $file
     * @param string|null $name
     * @return void
     */
    function download_file(string $file, ?string $name = null): void
    {
        if (!file_exists($file)) {
            not_found('File not found');
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

if (!function_exists('redirect_to_url')) {
    /**
     * Redirect to URL
     * @param string $url
     * @param int $status
     * @return void
     */
    function redirect_to_url(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }
}

if (!function_exists('base_url')) {
    /**
     * Get base URL
     * @param string $path
     * @return string
     */
    function base_url(string $path = ''): string
    {
        $protocol = is_https() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = $protocol . '://' . $host;
        
        if ($path) {
            $base .= '/' . ltrim($path, '/');
        }
        
        return $base;
    }
}
