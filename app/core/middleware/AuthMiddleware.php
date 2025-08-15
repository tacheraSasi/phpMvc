<?php

namespace Core\Middleware;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Simple JWT Authentication middleware
 */
class AuthMiddleware
{
    public static function handle()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (empty($authHeader)) {
            self::unauthorized('Authorization header missing');
        }
        
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            self::unauthorized('Invalid authorization header format');
        }
        
        $token = $matches[1];
        
        if (!self::validateToken($token)) {
            self::unauthorized('Invalid or expired token');
        }
        
        // Token is valid, set user context if needed
        $_REQUEST['authenticated_user'] = self::getUserFromToken($token);
    }
    
    protected static function validateToken($token)
    {
        // Simple token validation (in production, use proper JWT library)
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Decode payload
        $decodedPayload = json_decode(base64_decode($payload), true);
        
        if (!$decodedPayload) {
            return false;
        }
        
        // Check expiration
        if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
            return false;
        }
        
        // In production, verify signature with secret key
        $secret = \Config::get('security.jwt.secret', 'your-secret-key');
        $expectedSignature = base64_encode(hash_hmac('sha256', $header . '.' . $payload, $secret, true));
        
        return hash_equals($signature, $expectedSignature);
    }
    
    protected static function getUserFromToken($token)
    {
        $parts = explode('.', $token);
        $payload = json_decode(base64_decode($parts[1]), true);
        
        return [
            'id' => $payload['user_id'] ?? null,
            'email' => $payload['email'] ?? null,
            'name' => $payload['name'] ?? null
        ];
    }
    
    protected static function unauthorized($message = 'Unauthorized')
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }
    
    public static function generateToken($user)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'] ?? '',
            'iat' => time(),
            'exp' => time() + \Config::get('security.jwt.expiry', 3600)
        ]);
        
        $headerEncoded = base64_encode($header);
        $payloadEncoded = base64_encode($payload);
        
        $secret = \Config::get('security.jwt.secret', 'your-secret-key');
        $signature = base64_encode(hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret, true));
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }
}