<?php

namespace Core\Facades;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * DB Facade - Provides static access to Database operations
 * 
 * Usage:
 * DB::query("SELECT * FROM users WHERE id = ?", [1]);
 * DB::table('users')->where('email', $email)->first();
 */
class DB extends Facade
{
    /**
     * Get the accessor name for the facade
     * @return string
     */
    protected static function getAccessor(): string
    {
        return 'Database';
    }

    /**
     * Get database instance
     * @return \Database
     */
    protected static function getFacadeInstance()
    {
        static $instance = null;
        
        if ($instance === null) {
            $instance = new \Database();
        }
        
        return $instance;
    }

    /**
     * Execute a raw query
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public static function query(string $query, array $params = [])
    {
        return static::getFacadeInstance()->query($query, $params);
    }

    /**
     * Get table for query builder
     * @param string $table
     * @return \Database
     */
    public static function table(string $table)
    {
        $db = static::getFacadeInstance();
        $db->table = $table;
        return $db;
    }

    /**
     * Begin transaction
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return static::getFacadeInstance()->beginTransaction();
    }

    /**
     * Commit transaction
     * @return bool
     */
    public static function commit(): bool
    {
        return static::getFacadeInstance()->commit();
    }

    /**
     * Rollback transaction
     * @return bool
     */
    public static function rollback(): bool
    {
        return static::getFacadeInstance()->rollback();
    }
}
