<?php

namespace Core\Facades;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Request Facade - Provides static access to HTTP request data
 * 
 * Usage:
 * Request::get('key', 'default');
 * Request::all();
 * Request::has('key');
 */
class Request extends Facade
{
    /**
     * Get the accessor name for the facade
     * @return string
     */
    protected static function getAccessor(): string
    {
        return 'Request';
    }

    /**
     * Get input value
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_REQUEST;
        }
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Get all input data
     * @return array
     */
    public static function all(): array
    {
        return $_REQUEST;
    }

    /**
     * Check if input key exists
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_REQUEST[$key]);
    }

    /**
     * Get only specified keys from input
     * @param array $keys
     * @return array
     */
    public static function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if (isset($_REQUEST[$key])) {
                $result[$key] = $_REQUEST[$key];
            }
        }
        return $result;
    }

    /**
     * Get all input except specified keys
     * @param array $keys
     * @return array
     */
    public static function except(array $keys): array
    {
        $result = $_REQUEST;
        foreach ($keys as $key) {
            unset($result[$key]);
        }
        return $result;
    }

    /**
     * Get query parameter
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function query(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Get POST data
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function post(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Get JSON input
     * @return array|null
     */
    public static function json(): ?array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    /**
     * Get request method
     * @return string
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Check if request method matches
     * @param string $method
     * @return bool
     */
    public static function isMethod(string $method): bool
    {
        return strtoupper(self::method()) === strtoupper($method);
    }

    /**
     * Check if request is GET
     * @return bool
     */
    public static function isGet(): bool
    {
        return self::isMethod('GET');
    }

    /**
     * Check if request is POST
     * @return bool
     */
    public static function isPost(): bool
    {
        return self::isMethod('POST');
    }

    /**
     * Check if request is AJAX
     * @return bool
     */
    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get request header
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function header(string $key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Get all headers
     * @return array
     */
    public static function headers(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get client IP address
     * @return string
     */
    public static function ip(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     * @return string
     */
    public static function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Get request URI
     * @return string
     */
    public static function uri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Get request URL
     * @return string
     */
    public static function url(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . self::uri();
    }

    /**
     * Check if request is secure (HTTPS)
     * @return bool
     */
    public static function isSecure(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }

    /**
     * Get uploaded file
     * @param string $key
     * @return array|null
     */
    public static function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Check if file was uploaded
     * @param string $key
     * @return bool
     */
    public static function hasFile(string $key): bool
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK;
    }
}
