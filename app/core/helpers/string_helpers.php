<?php

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * String Helper Functions
 * Provides convenient string manipulation functions
 */

if (!function_exists('str_contains')) {
    /**
     * Check if string contains substring (for PHP < 8.0)
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * Check if string starts with substring (for PHP < 8.0)
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_starts_with(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * Check if string ends with substring (for PHP < 8.0)
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_ends_with(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length === 0) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }
}

if (!function_exists('str_limit')) {
    /**
     * Limit string to specified length
     * @param string $string
     * @param int $length
     * @param string $suffix
     * @return string
     */
    function str_limit(string $string, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($string) <= $length) {
            return $string;
        }
        return substr($string, 0, $length) . $suffix;
    }
}

if (!function_exists('str_slug')) {
    /**
     * Generate URL-friendly slug
     * @param string $string
     * @param string $separator
     * @return string
     */
    function str_slug(string $string, string $separator = '-'): string
    {
        $string = preg_replace('/[^a-z0-9]+/i', $separator, strtolower($string));
        return trim($string, $separator);
    }
}

if (!function_exists('str_random')) {
    /**
     * Generate random string
     * @param int $length
     * @return string
     */
    function str_random(int $length = 16): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('str_camel')) {
    /**
     * Convert string to camelCase
     * @param string $string
     * @return string
     */
    function str_camel(string $string): string
    {
        $string = str_replace(['-', '_'], ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        return lcfirst($string);
    }
}

if (!function_exists('str_snake')) {
    /**
     * Convert string to snake_case
     * @param string $string
     * @return string
     */
    function str_snake(string $string): string
    {
        $string = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);
        return strtolower($string);
    }
}

if (!function_exists('str_kebab')) {
    /**
     * Convert string to kebab-case
     * @param string $string
     * @return string
     */
    function str_kebab(string $string): string
    {
        $string = preg_replace('/([a-z])([A-Z])/', '$1-$2', $string);
        return strtolower($string);
    }
}

if (!function_exists('str_title')) {
    /**
     * Convert string to Title Case
     * @param string $string
     * @return string
     */
    function str_title(string $string): string
    {
        return ucwords(strtolower($string));
    }
}

if (!function_exists('str_words')) {
    /**
     * Limit string to specified number of words
     * @param string $string
     * @param int $words
     * @param string $suffix
     * @return string
     */
    function str_words(string $string, int $words = 100, string $suffix = '...'): string
    {
        $wordsArray = explode(' ', $string);
        if (count($wordsArray) <= $words) {
            return $string;
        }
        return implode(' ', array_slice($wordsArray, 0, $words)) . $suffix;
    }
}

if (!function_exists('str_mask')) {
    /**
     * Mask a string (useful for sensitive data)
     * @param string $string
     * @param int $visibleChars
     * @param string $mask
     * @return string
     */
    function str_mask(string $string, int $visibleChars = 4, string $mask = '*'): string
    {
        if (strlen($string) <= $visibleChars) {
            return $string;
        }
        return substr($string, 0, $visibleChars) . str_repeat($mask, strlen($string) - $visibleChars);
    }
}

if (!function_exists('str_uuid')) {
    /**
     * Generate UUID v4
     * @return string
     */
    function str_uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('str_escape')) {
    /**
     * Escape HTML entities
     * @param string $string
     * @return string
     */
    function str_escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('str_after')) {
    /**
     * Get substring after first occurrence of needle
     * @param string $string
     * @param string $needle
     * @return string
     */
    function str_after(string $string, string $needle): string
    {
        $pos = strpos($string, $needle);
        if ($pos === false) {
            return $string;
        }
        return substr($string, $pos + strlen($needle));
    }
}

if (!function_exists('str_before')) {
    /**
     * Get substring before first occurrence of needle
     * @param string $string
     * @param string $needle
     * @return string
     */
    function str_before(string $string, string $needle): string
    {
        $pos = strpos($string, $needle);
        if ($pos === false) {
            return $string;
        }
        return substr($string, 0, $pos);
    }
}
