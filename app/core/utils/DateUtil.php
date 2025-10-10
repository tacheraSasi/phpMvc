<?php

namespace Core\Utils;

use DateTime;
use DateTimeZone;
use Exception;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * DateUtil - Date and time utilities
 * Provides common date/time operations for API development
 */
class DateUtil
{
    /**
     * Get current timestamp
     * @return int
     */
    public static function now(): int
    {
        return time();
    }

    /**
     * Get current date in specified format
     * @param string $format
     * @param string|null $timezone
     * @return string
     */
    public static function currentDate(string $format = 'Y-m-d H:i:s', ?string $timezone = null): string
    {
        $date = new DateTime('now', $timezone ? new DateTimeZone($timezone) : null);
        return $date->format($format);
    }

    /**
     * Format date string
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function format(string $date, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dateTime = new DateTime($date);
            return $dateTime->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Get human-readable time difference
     * @param string $date
     * @return string
     */
    public static function timeAgo(string $date): string
    {
        try {
            $dateTime = new DateTime($date);
            $now = new DateTime();
            $diff = $now->diff($dateTime);

            if ($diff->y > 0) {
                return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
            }
            if ($diff->m > 0) {
                return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
            }
            if ($diff->d > 0) {
                return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
            }
            if ($diff->h > 0) {
                return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            }
            if ($diff->i > 0) {
                return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
            }
            return 'just now';
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Add days to date
     * @param string $date
     * @param int $days
     * @param string $format
     * @return string
     */
    public static function addDays(string $date, int $days, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dateTime = new DateTime($date);
            $dateTime->modify("+{$days} days");
            return $dateTime->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Subtract days from date
     * @param string $date
     * @param int $days
     * @param string $format
     * @return string
     */
    public static function subDays(string $date, int $days, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dateTime = new DateTime($date);
            $dateTime->modify("-{$days} days");
            return $dateTime->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Check if date is in the past
     * @param string $date
     * @return bool
     */
    public static function isPast(string $date): bool
    {
        try {
            $dateTime = new DateTime($date);
            $now = new DateTime();
            return $dateTime < $now;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if date is in the future
     * @param string $date
     * @return bool
     */
    public static function isFuture(string $date): bool
    {
        try {
            $dateTime = new DateTime($date);
            $now = new DateTime();
            return $dateTime > $now;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get start of day
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public static function startOfDay(?string $date = null, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dateTime = $date ? new DateTime($date) : new DateTime();
            $dateTime->setTime(0, 0, 0);
            return $dateTime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get end of day
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public static function endOfDay(?string $date = null, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dateTime = $date ? new DateTime($date) : new DateTime();
            $dateTime->setTime(23, 59, 59);
            return $dateTime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get difference in days between two dates
     * @param string $date1
     * @param string $date2
     * @return int
     */
    public static function diffInDays(string $date1, string $date2): int
    {
        try {
            $dateTime1 = new DateTime($date1);
            $dateTime2 = new DateTime($date2);
            $diff = $dateTime1->diff($dateTime2);
            return (int)$diff->format('%a');
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Convert date to ISO 8601 format
     * @param string $date
     * @return string
     */
    public static function toIso8601(string $date): string
    {
        return self::format($date, 'c');
    }

    /**
     * Parse ISO 8601 date
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function fromIso8601(string $date, string $format = 'Y-m-d H:i:s'): string
    {
        return self::format($date, $format);
    }

    /**
     * Check if date is valid
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function isValid(string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        try {
            $dateTime = DateTime::createFromFormat($format, $date);
            return $dateTime && $dateTime->format($format) === $date;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get timestamp from date
     * @param string $date
     * @return int
     */
    public static function toTimestamp(string $date): int
    {
        try {
            $dateTime = new DateTime($date);
            return $dateTime->getTimestamp();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Create date from timestamp
     * @param int $timestamp
     * @param string $format
     * @return string
     */
    public static function fromTimestamp(int $timestamp, string $format = 'Y-m-d H:i:s'): string
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        return $dateTime->format($format);
    }
}
