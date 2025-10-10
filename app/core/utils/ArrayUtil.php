<?php

namespace Core\Utils;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * ArrayUtil - Array manipulation utilities
 * Provides common array operations for API development
 */
class ArrayUtil
{
    /**
     * Get value from array using dot notation
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(array $array, string $key, $default = null)
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

    /**
     * Set value in array using dot notation
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public static function set(array &$array, string $key, $value): array
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

    /**
     * Check if key exists in array using dot notation
     * @param array $array
     * @param string $key
     * @return bool
     */
    public static function has(array $array, string $key): bool
    {
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }
        return true;
    }

    /**
     * Remove key from array using dot notation
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function forget(array &$array, string $key): array
    {
        $keys = explode('.', $key);
        $current = &$array;

        while (count($keys) > 1) {
            $k = array_shift($keys);
            if (!isset($current[$k]) || !is_array($current[$k])) {
                return $array;
            }
            $current = &$current[$k];
        }

        unset($current[array_shift($keys)]);
        return $array;
    }

    /**
     * Flatten a multi-dimensional array
     * @param array $array
     * @param int $depth
     * @return array
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1 ? array_values($item) : self::flatten($item, $depth - 1);
                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Filter array by keys
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Filter array excluding keys
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Pluck values from array of arrays/objects
     * @param array $array
     * @param string $key
     * @param string|null $indexKey
     * @return array
     */
    public static function pluck(array $array, string $key, ?string $indexKey = null): array
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

    /**
     * Group array by key
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function groupBy(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            $groupKey = is_object($item) ? $item->$key : $item[$key];
            $result[$groupKey][] = $item;
        }

        return $result;
    }

    /**
     * Check if array is associative
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        if ([] === $array) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Chunk array into smaller arrays
     * @param array $array
     * @param int $size
     * @return array
     */
    public static function chunk(array $array, int $size): array
    {
        return array_chunk($array, $size);
    }

    /**
     * Shuffle array while preserving keys
     * @param array $array
     * @return array
     */
    public static function shuffle(array $array): array
    {
        $keys = array_keys($array);
        shuffle($keys);
        $shuffled = [];
        foreach ($keys as $key) {
            $shuffled[$key] = $array[$key];
        }
        return $shuffled;
    }

    /**
     * Get first element of array
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    public static function first(array $array, $default = null)
    {
        if (empty($array)) {
            return $default;
        }
        return reset($array);
    }

    /**
     * Get last element of array
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    public static function last(array $array, $default = null)
    {
        if (empty($array)) {
            return $default;
        }
        return end($array);
    }

    /**
     * Wrap value in array if not already an array
     * @param mixed $value
     * @return array
     */
    public static function wrap($value): array
    {
        if (is_null($value)) {
            return [];
        }
        return is_array($value) ? $value : [$value];
    }
}
