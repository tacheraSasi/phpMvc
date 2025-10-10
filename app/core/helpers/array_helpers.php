<?php

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Array Helper Functions
 * Provides convenient array manipulation functions
 */

if (!function_exists('array_get')) {
    /**
     * Get value from array using dot notation
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_get(array $array, string $key, $default = null)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_set')) {
    /**
     * Set value in array using dot notation
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array
     */
    function array_set(array &$array, string $key, $value): array
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
        return $array;
    }
}

if (!function_exists('array_has')) {
    /**
     * Check if key exists in array using dot notation
     * @param array $array
     * @param string $key
     * @return bool
     */
    function array_has(array $array, string $key): bool
    {
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }
        return true;
    }
}

if (!function_exists('array_only')) {
    /**
     * Get only specified keys from array
     * @param array $array
     * @param array $keys
     * @return array
     */
    function array_only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }
}

if (!function_exists('array_except')) {
    /**
     * Get all keys except specified from array
     * @param array $array
     * @param array $keys
     * @return array
     */
    function array_except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }
}

if (!function_exists('array_pluck')) {
    /**
     * Pluck values from array of arrays/objects
     * @param array $array
     * @param string $key
     * @param string|null $indexKey
     * @return array
     */
    function array_pluck(array $array, string $key, ?string $indexKey = null): array
    {
        $result = [];

        foreach ($array as $item) {
            $value = is_object($item) ? $item->$key : $item[$key];
            
            if ($indexKey === null) {
                $result[] = $value;
            } else {
                $index = is_object($item) ? $item->$indexKey : $item[$indexKey];
                $result[$index] = $value;
            }
        }

        return $result;
    }
}

if (!function_exists('array_flatten')) {
    /**
     * Flatten a multi-dimensional array
     * @param array $array
     * @param int $depth
     * @return array
     */
    function array_flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1 ? array_values($item) : array_flatten($item, $depth - 1);
                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}

if (!function_exists('array_first')) {
    /**
     * Get first element of array
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    function array_first(array $array, $default = null)
    {
        if (empty($array)) {
            return $default;
        }
        return reset($array);
    }
}

if (!function_exists('array_last')) {
    /**
     * Get last element of array
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    function array_last(array $array, $default = null)
    {
        if (empty($array)) {
            return $default;
        }
        return end($array);
    }
}

if (!function_exists('array_wrap')) {
    /**
     * Wrap value in array if not already an array
     * @param mixed $value
     * @return array
     */
    function array_wrap($value): array
    {
        if (is_null($value)) {
            return [];
        }
        return is_array($value) ? $value : [$value];
    }
}

if (!function_exists('array_is_assoc')) {
    /**
     * Check if array is associative
     * @param array $array
     * @return bool
     */
    function array_is_assoc(array $array): bool
    {
        if ([] === $array) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
