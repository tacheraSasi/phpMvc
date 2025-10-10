<?php

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

use Core\Utils\StringUtil;
use Core\Utils\ArrayUtil;
use Core\Utils\DateUtil;
use Core\Utils\CryptoUtil;
use Core\Facades\Response;
use Core\Facades\Request;
use Core\Facades\Cache;

/**
 * UtilsDemoApi - Example controller demonstrating utils and facades
 * 
 * This controller shows practical usage of the new utilities in an API context
 */
class UtilsDemoApi
{
    use MainController;

    /**
     * Example 1: String utilities for data processing
     * GET /api/utils/string
     */
    public function stringDemo()
    {
        $text = Request::get('text', 'Hello World PHP Framework');
        
        $result = [
            'original' => $text,
            'slug' => StringUtil::slug($text),
            'camelCase' => StringUtil::camelCase($text),
            'snakeCase' => StringUtil::snakeCase($text),
            'truncated' => StringUtil::truncate($text, 20),
            'uuid' => StringUtil::uuid(),
            'masked' => StringUtil::mask('1234567890abcdef', 4)
        ];

        Response::success($result, 'String utilities demo');
    }

    /**
     * Example 2: Array utilities for data manipulation
     * POST /api/utils/array
     */
    public function arrayDemo()
    {
        $data = Request::json() ?? [
            'users' => [
                ['name' => 'John', 'email' => 'john@test.com', 'age' => 30],
                ['name' => 'Jane', 'email' => 'jane@test.com', 'age' => 25]
            ]
        ];

        $result = [
            'emails' => ArrayUtil::pluck($data['users'], 'email'),
            'first_user' => ArrayUtil::first($data['users']),
            'user_names' => array_pluck($data['users'], 'name'),
            'nested_access' => ArrayUtil::get($data, 'users.0.name', 'N/A')
        ];

        Response::success($result, 'Array utilities demo');
    }

    /**
     * Example 3: Date utilities for timestamps
     * GET /api/utils/date
     */
    public function dateDemo()
    {
        $date = Request::get('date', '2024-01-01');
        
        $result = [
            'original' => $date,
            'formatted' => DateUtil::format($date, 'd M Y'),
            'time_ago' => DateUtil::timeAgo($date),
            'add_7_days' => DateUtil::addDays($date, 7),
            'iso8601' => DateUtil::toIso8601($date),
            'current' => DateUtil::currentDate(),
            'is_past' => DateUtil::isPast($date),
            'timestamp' => DateUtil::toTimestamp($date)
        ];

        Response::success($result, 'Date utilities demo');
    }

    /**
     * Example 4: Crypto utilities for security
     * POST /api/utils/crypto
     */
    public function cryptoDemo()
    {
        $password = Request::get('password', 'secret123');
        
        // Hash password
        $hash = CryptoUtil::hashPassword($password);
        
        // Generate tokens
        $token = CryptoUtil::randomToken(16);
        $apiKey = CryptoUtil::generateApiKey('demo', 16);
        
        // Generate JWT (example)
        $jwtPayload = ['user_id' => 1, 'email' => 'demo@test.com'];
        $jwtToken = CryptoUtil::generateJWT($jwtPayload, 'secret-key', 3600);
        
        $result = [
            'password_hash' => substr($hash, 0, 30) . '...',
            'password_verified' => CryptoUtil::verifyPassword($password, $hash),
            'random_token' => $token,
            'api_key' => $apiKey,
            'jwt_token' => substr($jwtToken, 0, 50) . '...',
            'hash_sha256' => CryptoUtil::hash('Hello World', 'sha256')
        ];

        Response::success($result, 'Crypto utilities demo');
    }

    /**
     * Example 5: Using Cache facade
     * GET /api/utils/cache
     */
    public function cacheDemo()
    {
        $key = 'demo_data';
        
        // Check if cached
        if (Cache::has($key)) {
            $data = Cache::get($key);
            $cached = true;
        } else {
            // Simulate expensive operation
            $data = [
                'timestamp' => time(),
                'message' => 'This data was generated and cached',
                'random' => str_random(10)
            ];
            
            // Cache for 60 seconds
            Cache::put($key, $data, 60);
            $cached = false;
        }

        Response::success([
            'cached' => $cached,
            'data' => $data,
            'cache_stats' => [
                'has_key' => Cache::has($key),
                'ttl' => '60 seconds'
            ]
        ], 'Cache demo');
    }

    /**
     * Example 6: Combined utilities in a real scenario
     * POST /api/utils/user-profile
     */
    public function userProfileDemo()
    {
        // Get request data
        $input = Request::all();
        
        // Validate using helper
        $required = ['name', 'email', 'bio'];
        $missing = [];
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            Response::validationError(
                ['required_fields' => $missing],
                'Missing required fields'
            );
        }

        // Process data using utilities
        $profile = [
            'id' => StringUtil::uuid(),
            'name' => $input['name'],
            'slug' => StringUtil::slug($input['name']),
            'email' => $input['email'],
            'bio' => StringUtil::truncate($input['bio'], 200),
            'created_at' => DateUtil::currentDate(),
            'api_key' => CryptoUtil::generateApiKey('usr', 16)
        ];

        // Cache the profile
        $cacheKey = 'user_profile_' . $profile['id'];
        Cache::put($cacheKey, $profile, 3600);

        Response::created($profile, 'User profile created successfully');
    }

    /**
     * Example 7: Helper functions demonstration
     * GET /api/utils/helpers
     */
    public function helpersDemo()
    {
        $data = [
            'string_helpers' => [
                'uuid' => str_uuid(),
                'random' => str_random(12),
                'slug' => str_slug('Hello PHP World'),
                'camel' => str_camel('hello-world'),
                'snake' => str_snake('HelloWorld')
            ],
            'array_helpers' => [
                'first' => array_first([1, 2, 3]),
                'last' => array_last([1, 2, 3]),
                'flatten' => array_flatten([[1, 2], [3, 4]])
            ],
            'http_helpers' => [
                'method' => request_method(),
                'is_get' => is_get(),
                'is_https' => is_https(),
                'client_ip' => client_ip(),
                'user_agent' => substr(user_agent(), 0, 50) . '...'
            ]
        ];

        Response::success($data, 'Helper functions demo');
    }

    /**
     * Clear cache
     * DELETE /api/utils/cache
     */
    public function clearCache()
    {
        Cache::flush();
        Response::success([], 'Cache cleared successfully');
    }
}
