<?php

namespace Core\Utils;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * CryptoUtil - Encryption and hashing utilities
 * Provides cryptographic operations for API development
 */
class CryptoUtil
{
    /**
     * Hash password using bcrypt
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify password against hash
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate secure random token
     * @param int $length
     * @return string
     */
    public static function randomToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate hash using specified algorithm
     * @param string $data
     * @param string $algorithm
     * @return string
     */
    public static function hash(string $data, string $algorithm = 'sha256'): string
    {
        return hash($algorithm, $data);
    }

    /**
     * Generate HMAC
     * @param string $data
     * @param string $key
     * @param string $algorithm
     * @return string
     */
    public static function hmac(string $data, string $key, string $algorithm = 'sha256'): string
    {
        return hash_hmac($algorithm, $data, $key);
    }

    /**
     * Encrypt data using AES-256-CBC
     * @param string $data
     * @param string $key
     * @return string|false
     */
    public static function encrypt(string $data, string $key)
    {
        $key = hash('sha256', $key, true);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        
        if ($encrypted === false) {
            return false;
        }
        
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * Decrypt data using AES-256-CBC
     * @param string $data
     * @param string $key
     * @return string|false
     */
    public static function decrypt(string $data, string $key)
    {
        $key = hash('sha256', $key, true);
        $parts = explode('::', base64_decode($data), 2);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        [$encrypted, $iv] = $parts;
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }

    /**
     * Generate JWT token
     * @param array $payload
     * @param string $secret
     * @param int $expiry
     * @return string
     */
    public static function generateJWT(array $payload, string $secret, int $expiry = 3600): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + $expiry;
        $payload = json_encode($payload);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    /**
     * Verify JWT token
     * @param string $token
     * @param string $secret
     * @return array|false
     */
    public static function verifyJWT(string $token, string $secret)
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;

        $validSignature = hash_hmac('sha256', $header . '.' . $payload, $secret, true);
        $validSignature = self::base64UrlEncode($validSignature);

        if ($signature !== $validSignature) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($payload), true);

        if (isset($payload['exp']) && time() > $payload['exp']) {
            return false;
        }

        return $payload;
    }

    /**
     * Base64 URL encode
     * @param string $data
     * @return string
     */
    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     * @param string $data
     * @return string
     */
    protected static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    /**
     * Generate API key
     * @param string $prefix
     * @param int $length
     * @return string
     */
    public static function generateApiKey(string $prefix = 'sk', int $length = 32): string
    {
        return $prefix . '_' . self::randomToken($length);
    }

    /**
     * Generate checksum for data
     * @param string $data
     * @param string $algorithm
     * @return string
     */
    public static function checksum(string $data, string $algorithm = 'md5'): string
    {
        return hash($algorithm, $data);
    }

    /**
     * Compare two strings in constant time
     * @param string $known
     * @param string $user
     * @return bool
     */
    public static function secureCompare(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }
}
