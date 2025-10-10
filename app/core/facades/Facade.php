<?php

namespace Core\Facades;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Facade - Base class for facades
 * Provides static access to underlying classes
 */
abstract class Facade
{
    /**
     * Get the accessor name for the facade
     * @return string
     */
    protected static function getAccessor(): string
    {
        throw new \RuntimeException('Facade does not implement getAccessor method.');
    }

    /**
     * Get the instance behind the facade
     * @return mixed
     */
    protected static function getFacadeInstance()
    {
        $accessor = static::getAccessor();
        
        if (class_exists($accessor)) {
            return new $accessor();
        }
        
        throw new \RuntimeException("Facade accessor [{$accessor}] not found.");
    }

    /**
     * Handle dynamic static calls
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        $instance = static::getFacadeInstance();
        
        if (!method_exists($instance, $method)) {
            throw new \RuntimeException("Method [{$method}] does not exist on " . get_class($instance));
        }
        
        return $instance->$method(...$args);
    }
}
