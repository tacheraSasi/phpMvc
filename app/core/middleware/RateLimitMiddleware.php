<?php

namespace Core\Middleware;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Simple Rate Limiting middleware
 */
class RateLimitMiddleware
{
    protected static $storageFile = '/tmp/rate_limits.json';
    
    public static function handle($maxRequests = 100, $perMinutes = 60)
    {
        if (!\Config::get('api.rate_limit.enabled', false)) {
            return;
        }
        
        $maxRequests = \Config::get('api.rate_limit.max_requests', $maxRequests);
        $perMinutes = \Config::get('api.rate_limit.per_minutes', $perMinutes);
        
        $clientId = self::getClientId();
        $now = time();
        $windowStart = $now - ($perMinutes * 60);
        
        $rateLimits = self::loadRateLimits();
        
        // Clean old entries
        $rateLimits = self::cleanOldEntries($rateLimits, $windowStart);
        
        // Initialize client data if not exists
        if (!isset($rateLimits[$clientId])) {
            $rateLimits[$clientId] = [];
        }
        
        // Count requests in current window
        $requestsInWindow = count(array_filter($rateLimits[$clientId], function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        }));
        
        if ($requestsInWindow >= $maxRequests) {
            self::rateLimitExceeded($maxRequests, $perMinutes);
        }
        
        // Add current request
        $rateLimits[$clientId][] = $now;
        
        // Save rate limits
        self::saveRateLimits($rateLimits);
        
        // Add rate limit headers
        $remaining = max(0, $maxRequests - $requestsInWindow - 1);
        $resetTime = $windowStart + ($perMinutes * 60);
        
        header("X-RateLimit-Limit: {$maxRequests}");
        header("X-RateLimit-Remaining: {$remaining}");
        header("X-RateLimit-Reset: {$resetTime}");
    }
    
    protected static function getClientId()
    {
        // Use IP address as client identifier
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
              $_SERVER['HTTP_X_REAL_IP'] ?? 
              $_SERVER['REMOTE_ADDR'] ?? 
              'unknown';
        
        // If there's an authenticated user, use their ID instead
        if (isset($_REQUEST['authenticated_user']['id'])) {
            return 'user_' . $_REQUEST['authenticated_user']['id'];
        }
        
        return 'ip_' . $ip;
    }
    
    protected static function loadRateLimits()
    {
        if (!file_exists(self::$storageFile)) {
            return [];
        }
        
        $data = file_get_contents(self::$storageFile);
        return json_decode($data, true) ?: [];
    }
    
    protected static function saveRateLimits($rateLimits)
    {
        file_put_contents(self::$storageFile, json_encode($rateLimits));
    }
    
    protected static function cleanOldEntries($rateLimits, $windowStart)
    {
        foreach ($rateLimits as $clientId => $timestamps) {
            $rateLimits[$clientId] = array_filter($timestamps, function($timestamp) use ($windowStart) {
                return $timestamp > $windowStart;
            });
            
            // Remove clients with no recent requests
            if (empty($rateLimits[$clientId])) {
                unset($rateLimits[$clientId]);
            }
        }
        
        return $rateLimits;
    }
    
    protected static function rateLimitExceeded($maxRequests, $perMinutes)
    {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Rate limit exceeded',
            'message' => "Maximum {$maxRequests} requests per {$perMinutes} minutes allowed"
        ]);
        exit;
    }
}