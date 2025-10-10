<?php

namespace Core\Utils;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * StringUtil - String manipulation utilities
 * Provides common string operations for API development
 */
class StringUtil
{
    /**
     * Convert string to camelCase
     * @param string $string
     * @return string
     */
    public static function camelCase(string $string): string
    {
        $string = str_replace(['-', '_'], ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        return lcfirst($string);
    }

    /**
     * Convert string to snake_case
     * @param string $string
     * @return string
     */
    public static function snakeCase(string $string): string
    {
        $string = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);
        return strtolower($string);
    }

    /**
     * Convert string to kebab-case
     * @param string $string
     * @return string
     */
    public static function kebabCase(string $string): string
    {
        $string = preg_replace('/([a-z])([A-Z])/', '$1-$2', $string);
        return strtolower($string);
    }

    /**
     * Generate a random string
     * @param int $length
     * @param string $characters
     * @return string
     */
    public static function random(int $length = 16, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Truncate string to specified length
     * @param string $string
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate(string $string, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($string) <= $length) {
            return $string;
        }
        return substr($string, 0, $length) . $suffix;
    }

    /**
     * Check if string starts with given substring
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }

    /**
     * Check if string ends with given substring
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length === 0) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }

    /**
     * Check if string contains given substring
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Slugify a string for URLs
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function slug(string $string, string $separator = '-'): string
    {
        $string = preg_replace('/[^a-z0-9]+/i', $separator, strtolower($string));
        return trim($string, $separator);
    }

    /**
     * Generate UUID v4
     * @return string
     */
    public static function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Mask a string (useful for hiding sensitive data)
     * @param string $string
     * @param int $visibleChars
     * @param string $mask
     * @return string
     */
    public static function mask(string $string, int $visibleChars = 4, string $mask = '*'): string
    {
        if (strlen($string) <= $visibleChars) {
            return $string;
        }
        return substr($string, 0, $visibleChars) . str_repeat($mask, strlen($string) - $visibleChars);
    }

    /**
     * Convert string to title case
     * @param string $string
     * @return string
     */
    public static function titleCase(string $string): string
    {
        return ucwords(strtolower($string));
    }

    /**
     * Limit string to specified number of words
     * @param string $string
     * @param int $words
     * @param string $suffix
     * @return string
     */
    public static function limitWords(string $string, int $words = 100, string $suffix = '...'): string
    {
        $wordsArray = explode(' ', $string);
        if (count($wordsArray) <= $words) {
            return $string;
        }
        return implode(' ', array_slice($wordsArray, 0, $words)) . $suffix;
    }
}
