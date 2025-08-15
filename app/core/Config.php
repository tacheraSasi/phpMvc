<?php

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Configuration manager with environment support
 */
class Config
{
    protected static $config = [];
    protected static $loaded = false;

    public static function load()
    {
        if (self::$loaded) {
            return;
        }

        // Load environment-specific configuration
        $env = $_ENV['APP_ENV'] ?? 'development';
        
        // Default configuration
        self::$config = [
            'app' => [
                'name' => APP_NAME ?? 'PHP MVC Framework',
                'description' => APP_DESC ?? 'Modern PHP MVC Framework for APIs',
                'version' => '1.0.0',
                'env' => $env,
                'debug' => DEBUG ?? true,
                'timezone' => 'UTC'
            ],
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => [
                        'driver' => 'mysql',
                        'host' => DBHOST ?? 'localhost',
                        'database' => DBNAME ?? 'mvc_db',
                        'username' => DBUSER ?? 'root',
                        'password' => DBPASS ?? '',
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci'
                    ]
                ]
            ],
            'api' => [
                'version' => 'v1',
                'rate_limit' => [
                    'enabled' => false,
                    'max_requests' => 100,
                    'per_minutes' => 60
                ],
                'cors' => [
                    'enabled' => true,
                    'allowed_origins' => ['*'],
                    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
                    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With']
                ]
            ],
            'security' => [
                'jwt' => [
                    'secret' => $_ENV['JWT_SECRET'] ?? 'your-secret-key',
                    'expiry' => 3600 // 1 hour
                ]
            ]
        ];

        self::$loaded = true;
    }

    public static function get($key, $default = null)
    {
        self::load();
        
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public static function set($key, $value)
    {
        self::load();
        
        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $k) {
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    public static function all()
    {
        self::load();
        return self::$config;
    }
}