<?php

namespace Core\Facades;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Validator Facade - Provides static access to validation
 * 
 * Usage:
 * $validator = Validator::make($data, $rules);
 * if ($validator->fails()) { ... }
 */
class Validator extends Facade
{
    /**
     * Get the accessor name for the facade
     * @return string
     */
    protected static function getAccessor(): string
    {
        return 'Validator';
    }

    /**
     * Create a new validator instance
     * @param array $data
     * @param array $rules
     * @return \Validator
     */
    public static function make(array $data, array $rules)
    {
        return \Validator::make($data, $rules);
    }

    /**
     * Validate email
     * @param string $email
     * @return bool
     */
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     * @param string $url
     * @return bool
     */
    public static function url(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate IP address
     * @param string $ip
     * @return bool
     */
    public static function ip(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate required field
     * @param mixed $value
     * @return bool
     */
    public static function required($value): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        if (is_array($value) && count($value) < 1) {
            return false;
        }
        return true;
    }

    /**
     * Validate minimum length
     * @param string $value
     * @param int $min
     * @return bool
     */
    public static function min(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }

    /**
     * Validate maximum length
     * @param string $value
     * @param int $max
     * @return bool
     */
    public static function max(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }

    /**
     * Validate numeric value
     * @param mixed $value
     * @return bool
     */
    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Validate integer value
     * @param mixed $value
     * @return bool
     */
    public static function integer($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate alphabetic characters
     * @param string $value
     * @return bool
     */
    public static function alpha(string $value): bool
    {
        return preg_match('/^[a-zA-Z]+$/', $value) === 1;
    }

    /**
     * Validate alphanumeric characters
     * @param string $value
     * @return bool
     */
    public static function alphaNum(string $value): bool
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $value) === 1;
    }

    /**
     * Validate date format
     * @param string $value
     * @param string $format
     * @return bool
     */
    public static function date(string $value, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) === $value;
    }

    /**
     * Validate value is in array
     * @param mixed $value
     * @param array $values
     * @return bool
     */
    public static function in($value, array $values): bool
    {
        return in_array($value, $values, true);
    }

    /**
     * Validate value matches pattern
     * @param string $value
     * @param string $pattern
     * @return bool
     */
    public static function regex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }
}
