<?php

namespace Viper\Middleware;

use Viper\Http\Request;
use Viper\Http\Response;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Rate Limiting Middleware
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    protected int $maxRequests = 60;
    protected int $windowSeconds = 60;
    protected string $storageFile;
    
    public function __construct(int $maxRequests = 60, int $windowSeconds = 60)
    {
        $this->maxRequests = $maxRequests;
        $this->windowSeconds = $windowSeconds;
        $this->storageFile = sys_get_temp_dir() . '/viper_rate_limit.json';
    }
    
    public function handle(Request $request, callable $next): Response
    {
        $clientId = $this->getClientId($request);
        $currentTime = time();
        
        // Load rate limit data
        $data = $this->loadData();
        
        // Clean old entries
        $this->cleanOldEntries($data, $currentTime);
        
        // Check rate limit
        if (!isset($data[$clientId])) {
            $data[$clientId] = ['requests' => [], 'blocked_until' => 0];
        }
        
        $clientData = &$data[$clientId];
        
        // Check if still blocked
        if ($clientData['blocked_until'] > $currentTime) {
            $response = new Response();
            return $response->status(429)
                           ->header('Retry-After', (string)($clientData['blocked_until'] - $currentTime))
                           ->json(['error' => 'Rate limit exceeded']);
        }
        
        // Add current request
        $clientData['requests'][] = $currentTime;
        
        // Keep only requests within the window
        $clientData['requests'] = array_filter(
            $clientData['requests'],
            fn($time) => $time > ($currentTime - $this->windowSeconds)
        );
        
        // Check if limit exceeded
        if (count($clientData['requests']) > $this->maxRequests) {
            $clientData['blocked_until'] = $currentTime + $this->windowSeconds;
            $this->saveData($data);
            
            $response = new Response();
            return $response->status(429)
                           ->header('Retry-After', (string)$this->windowSeconds)
                           ->json(['error' => 'Rate limit exceeded']);
        }
        
        // Save data and continue
        $this->saveData($data);
        
        $response = $next($request);
        
        // Add rate limit headers
        $remaining = max(0, $this->maxRequests - count($clientData['requests']));
        $response->header('X-RateLimit-Limit', (string)$this->maxRequests)
                 ->header('X-RateLimit-Remaining', (string)$remaining)
                 ->header('X-RateLimit-Reset', (string)($currentTime + $this->windowSeconds));
        
        return $response;
    }
    
    protected function getClientId(Request $request): string
    {
        return md5($request->ip() . ':' . $request->userAgent());
    }
    
    protected function loadData(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        
        $content = file_get_contents($this->storageFile);
        return json_decode($content, true) ?: [];
    }
    
    protected function saveData(array $data): void
    {
        file_put_contents($this->storageFile, json_encode($data));
    }
    
    protected function cleanOldEntries(array &$data, int $currentTime): void
    {
        foreach ($data as $clientId => $clientData) {
            if ($clientData['blocked_until'] < $currentTime && 
                empty(array_filter($clientData['requests'], fn($time) => $time > ($currentTime - $this->windowSeconds)))) {
                unset($data[$clientId]);
            }
        }
    }
}