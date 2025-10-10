<?php

namespace Core\Utils;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * FileUtil - File system utilities
 * Provides common file operations for API development
 */
class FileUtil
{
    /**
     * Read file contents
     * @param string $path
     * @return string|false
     */
    public static function get(string $path)
    {
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return false;
    }

    /**
     * Write contents to file
     * @param string $path
     * @param string $contents
     * @return bool|int
     */
    public static function put(string $path, string $contents)
    {
        return file_put_contents($path, $contents);
    }

    /**
     * Append contents to file
     * @param string $path
     * @param string $contents
     * @return bool|int
     */
    public static function append(string $path, string $contents)
    {
        return file_put_contents($path, $contents, FILE_APPEND);
    }

    /**
     * Delete file
     * @param string $path
     * @return bool
     */
    public static function delete(string $path): bool
    {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    /**
     * Check if file exists
     * @param string $path
     * @return bool
     */
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Get file extension
     * @param string $path
     * @return string
     */
    public static function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get file name without extension
     * @param string $path
     * @return string
     */
    public static function name(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Get file name with extension
     * @param string $path
     * @return string
     */
    public static function basename(string $path): string
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Get directory name from path
     * @param string $path
     * @return string
     */
    public static function dirname(string $path): string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Get file size in bytes
     * @param string $path
     * @return int|false
     */
    public static function size(string $path)
    {
        if (file_exists($path)) {
            return filesize($path);
        }
        return false;
    }

    /**
     * Get file MIME type
     * @param string $path
     * @return string|false
     */
    public static function mimeType(string $path)
    {
        if (file_exists($path)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);
            return $mime;
        }
        return false;
    }

    /**
     * Get last modified timestamp
     * @param string $path
     * @return int|false
     */
    public static function lastModified(string $path)
    {
        if (file_exists($path)) {
            return filemtime($path);
        }
        return false;
    }

    /**
     * Copy file
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public static function copy(string $source, string $destination): bool
    {
        if (file_exists($source)) {
            return copy($source, $destination);
        }
        return false;
    }

    /**
     * Move file
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public static function move(string $source, string $destination): bool
    {
        if (file_exists($source)) {
            return rename($source, $destination);
        }
        return false;
    }

    /**
     * Create directory
     * @param string $path
     * @param int $permissions
     * @param bool $recursive
     * @return bool
     */
    public static function makeDirectory(string $path, int $permissions = 0755, bool $recursive = true): bool
    {
        if (!file_exists($path)) {
            return mkdir($path, $permissions, $recursive);
        }
        return false;
    }

    /**
     * Check if path is directory
     * @param string $path
     * @return bool
     */
    public static function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Check if path is file
     * @param string $path
     * @return bool
     */
    public static function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * List files in directory
     * @param string $directory
     * @param bool $recursive
     * @return array
     */
    public static function files(string $directory, bool $recursive = false): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_file($path)) {
                $files[] = $path;
            } elseif ($recursive && is_dir($path)) {
                $files = array_merge($files, self::files($path, true));
            }
        }

        return $files;
    }

    /**
     * Delete directory
     * @param string $directory
     * @param bool $preserve
     * @return bool
     */
    public static function deleteDirectory(string $directory, bool $preserve = false): bool
    {
        if (!is_dir($directory)) {
            return false;
        }

        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return $preserve ? true : rmdir($directory);
    }

    /**
     * Get human-readable file size
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function humanFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
