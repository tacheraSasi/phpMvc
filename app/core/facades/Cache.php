<?php

namespace Core\Facades;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Cache Facade - Provides simple file-based caching
 * 
 * Usage:
 * Cache::put('key', 'value', 3600);
 * Cache::get('key', 'default');
 * Cache::forget('key');
 */
class Cache extends Facade
{
    protected static $cacheDir = '../app/storage/cache/';
    
    /**
     * Get the accessor name for the facade
     * @return string
     */
    protected static function getAccessor(): string
    {
        return 'Cache';
    }

    /**
     * Initialize cache directory
     */
    protected static function init(): void
    {
        if (!file_exists(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
    }

    /**
     * Store data in cache
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time to live in seconds
     * @return bool
     */
    public static function put(string $key, $value, int $ttl = 3600): bool
    {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];
        return file_put_contents($file, serialize($data)) !== false;
    }

    /**
     * Get data from cache
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));
        
        if (time() > $data['expires']) {
            self::forget($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Check if cache key exists
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return false;
        }

        $data = unserialize(file_get_contents($file));
        
        if (time() > $data['expires']) {
            self::forget($key);
            return false;
        }

        return true;
    }

    /**
     * Remove data from cache
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    /**
     * Clear all cache
     * @return bool
     */
    public static function flush(): bool
    {
        self::init();
        $files = glob(self::$cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Get or store cache value
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public static function remember(string $key, callable $callback, int $ttl = 3600)
    {
        if (self::has($key)) {
            return self::get($key);
        }

        $value = $callback();
        self::put($key, $value, $ttl);
        return $value;
    }

    /**
     * Store cache forever (1 year)
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function forever(string $key, $value): bool
    {
        return self::put($key, $value, 31536000); // 1 year
    }
}
