<?php

namespace Viper\Core;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Environment and Configuration manager
 */
class Config
{
    protected static array $config = [];
    protected static bool $loaded = false;
    
    /**
     * Load environment file
     */
    public static function loadEnv(string $path = null): void
    {
        if (self::$loaded) {
            return;
        }
        
        $envFile = $path ?? dirname(ROOTPATH) . '/.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue; // Skip comments
                }
                
                if (strpos($line, '=') !== false) {
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes
                    $value = trim($value, '\"\'');
                    
                    // Set environment variable
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                    
                    // Store in config
                    self::$config[$key] = $value;
                }
            }
        }
        
        // Load default configuration
        self::loadDefaults();
        
        self::$loaded = true;
    }
    
    /**
     * Load default configuration values
     */
    protected static function loadDefaults(): void
    {
        $defaults = [
            'APP_NAME' => 'Viper Framework',
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'APP_URL' => 'http://localhost:8000',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => 'localhost',
            'DB_PORT' => '3306',
            'DB_DATABASE' => 'mvc_db',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
            'LOG_CHANNEL' => 'file',
            'LOG_LEVEL' => 'debug',
            'CORS_ALLOWED_ORIGINS' => '*',
            'RATE_LIMIT_REQUESTS' => '60',
            'RATE_LIMIT_WINDOW' => '60',
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset(self::$config[$key])) {
                self::$config[$key] = $value;
                $_ENV[$key] = $value;
            }
        }
    }
    
    /**
     * Get configuration value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::loadEnv();
        }
        
        // Support dot notation for nested config
        if (strpos($key, '.') !== false) {
            return self::getDotNotation($key, $default);
        }
        
        return self::$config[$key] ?? $_ENV[$key] ?? $default;
    }
    
    /**
     * Set configuration value
     */
    public static function set(string $key, mixed $value): void
    {
        self::$config[$key] = $value;
        $_ENV[$key] = $value;
    }
    
    /**
     * Get all configuration
     */
    public static function all(): array
    {
        if (!self::$loaded) {
            self::loadEnv();
        }
        
        return self::$config;
    }
    
    /**
     * Check if configuration key exists
     */
    public static function has(string $key): bool
    {
        if (!self::$loaded) {
            self::loadEnv();
        }
        
        return isset(self::$config[$key]) || isset($_ENV[$key]);
    }
    
    /**
     * Get value using dot notation
     */
    protected static function getDotNotation(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $segment) {
            if (is_array($value) && isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
    
    /**
     * Get environment value
     */
    public static function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
    
    /**
     * Check if app is in debug mode
     */
    public static function isDebug(): bool
    {
        return filter_var(self::get('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Get app environment
     */
    public static function environment(): string
    {
        return self::get('APP_ENV', 'production');
    }
}

/**
 * Helper function to get configuration value
 */
function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}