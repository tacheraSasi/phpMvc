# PHP MVC Framework - Enhanced for Modern API Development

A modern, lightweight PHP MVC framework enhanced with powerful features for building REST APIs, similar to NestJS architecture patterns.

## Features

### **Modern API Development**

- RESTful routing with parameter support
- JSON request/response handling
- CORS support out of the box
- Comprehensive validation system
- Standardized API response format

### **Security & Authentication**  

- JWT authentication middleware
- SQL injection protection with prepared statements
- Rate limiting middleware
- Input validation and sanitization

### **Developer Experience**

- Dependency injection container
- Middleware system for cross-cutting concerns
- Global error handling with debug mode
- Environment-aware configuration
- Fluent validation API

### **Core Components**

- Enhanced routing system with HTTP verb support
- **NEW:** Callback routes (closures) for quick API endpoints
- Request/Response abstraction
- Database abstraction layer
- Simple ORM with validation
- Autoloading system

## Quick Start

### Installation

```bash
git clone https://github.com/tacheraSasi/phpMvc.git
cd phpMvc
```

### Basic Setup

1. **Configure your web server** to point to the `public` directory
2. **Update database configuration** in `app/core/config.php`
3. **Set environment variables** (optional)

### Simple API Example

```php
<?php
// public/index.php

$app = new App;

// Basic routes
$app->get('api/hello', function() {
    return json_encode(['message' => 'Hello World!']);
});

// RESTful resource routes
$app->resource('api/users', UserApi::class);

// Custom routes with parameters
$app->get('api/users/{id}/posts', [UserApi::class, 'posts']);

$app->run();
```

## API Examples

### Authentication

```bash
# Register new user
POST /api/auth/register
Content-Type: application/json
{
    "name": "John Doe",
    "email": "john@example.com", 
    "password": "password123"
}

# Login
POST /api/auth/login
Content-Type: application/json
{
    "email": "john@example.com",
    "password": "password123"
}

# Get current user (requires auth)
GET /api/auth/me
Authorization: Bearer <token>
```

### User Management

```bash
# Get all users
GET /api/users

# Get specific user
GET /api/users/1

# Create user with validation
POST /api/users
Content-Type: application/json
{
    "name": "Jane Smith",
    "email": "jane@example.com"
}

# Update user
PUT /api/users/1
Content-Type: application/json
{
    "name": "Jane Updated"
}

# Delete user
DELETE /api/users/1
```

## Framework Architecture

### Directory Structure

``` shell
app/
├── controllers/          # HTTP Controllers
│   ├── Api.php          # API info endpoint
│   ├── UserApi.php      # User management API
│   └── AuthApi.php      # Authentication API
├── core/                # Core framework files
│   ├── App.php          # Main application class
│   ├── Router.php       # Enhanced routing system
│   ├── Controller.php   # Base controller with API helpers
│   ├── Model.php        # Base model with validation
│   ├── Request.php      # HTTP request abstraction
│   ├── Validator.php    # Validation system
│   ├── Container.php    # Dependency injection
│   ├── Config.php       # Configuration manager
│   ├── ErrorHandler.php # Global error handling
│   ├── middleware/      # Middleware components
│   │   ├── CorsMiddleware.php
│   │   ├── AuthMiddleware.php
│   │   ├── JsonMiddleware.php
│   │   └── RateLimitMiddleware.php
│   ├── utils/           # Utility classes
│   │   ├── StringUtil.php
│   │   ├── ArrayUtil.php
│   │   ├── DateUtil.php
│   │   ├── FileUtil.php
│   │   └── CryptoUtil.php
│   ├── facades/         # Static facades
│   │   ├── DB.php
│   │   ├── Cache.php
│   │   ├── Response.php
│   │   ├── Request.php
│   │   └── Validator.php
│   └── helpers/         # Helper functions
│       ├── api_helpers.php
│       ├── array_helpers.php
│       ├── string_helpers.php
│       └── http_helpers.php
├── models/              # Data models
├── storage/             # Storage directory
│   └── cache/          # File-based cache
└── views/               # View templates (for web routes)
public/
├── index.php           # Application entry point
└── .htaccess          # URL rewriting rules
```

### Core Components

#### Enhanced Router

```php
// Controller-based routes
$app->get('api/users', [UserApi::class, 'index']);
$app->post('api/users', [UserApi::class, 'store']);
$app->put('api/users/{id}', [UserApi::class, 'update']);
$app->delete('api/users/{id}', [UserApi::class, 'destroy']);

// NEW: Callback routes (closures)
$app->get('api/status', function($req, $res) {
    $res::json(['status' => 'online']);
});

$app->get('api/users/{id}', function($req, $res, $id) {
    $res::json(['user_id' => $id]);
});

// RESTful resource routing
$app->resource('api/users', UserApi::class);

// Middleware application
$app->get('api/protected', [ProtectedController::class])
    ->middleware('auth');
```

**📚 See [CALLBACK_ROUTES.md](CALLBACK_ROUTES.md) for complete callback route documentation**

#### API Response Helpers

```php
class UserApi {
    use MainController;
    
    public function index() {
        $users = User::all();
        $this->sendSuccess($users, 'Users retrieved successfully');
    }
    
    public function store() {
        $validator = Validator::make($request->allInput(), [
            'name' => 'required|min:2',
            'email' => 'required|email'
        ]);
        
        if ($validator->fails()) {
            $this->sendError('Validation failed', 422, $validator->getErrors());
        }
        
        // Create user logic...
        $this->sendSuccess($user, 'User created', 201);
    }
}
```

#### Validation System

```php
$validator = Validator::make($data, [
    'name' => 'required|min:2|max:50',
    'email' => 'required|email|unique:users',
    'age' => 'numeric|min:18',
    'status' => 'in:active,inactive'
]);

if ($validator->fails()) {
    return $validator->getErrors();
}
```

#### Middleware System

```php
// Apply middleware globally
CorsMiddleware::handle();
JsonMiddleware::handle();

// Rate limiting
RateLimitMiddleware::handle(100, 60); // 100 requests per 60 minutes

// Authentication
AuthMiddleware::handle(); // Validates JWT token
```

#### Configuration Management

```php
// Get configuration values
$dbHost = Config::get('database.connections.mysql.host');
$jwtSecret = Config::get('security.jwt.secret');

// Set configuration
Config::set('api.version', 'v2');
```

## Response Format

### Success Response

```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

### Error Response

```json
{
    "error": "Validation failed",
    "details": {
        "email": ["The email field is required"],
        "name": ["The name must be at least 2 characters"]
    }
}
```

### Validation Error (422)

```json
{
    "error": "Validation failed",
    "details": {
        "email": [
            "The email must be a valid email address"
        ],
        "name": [
            "The name must be at least 2 characters"
        ]
    }
}
```

## Security Features

### SQL Injection Protection

```php
// Automatic protection with prepared statements
$user = User::where('email', $email)->first();

// Or manual prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

### JWT Authentication

```php
// Generate token
$token = AuthMiddleware::generateToken($user);

// Validate token (automatic in middleware)
AuthMiddleware::handle();

// Access authenticated user
$user = $_REQUEST['authenticated_user'];
```

### Rate Limiting

```php
// Configure in config.php
'api' => [
    'rate_limit' => [
        'enabled' => true,
        'max_requests' => 100,
        'per_minutes' => 60
    ]
]
```

## Advanced Features

### Dependency Injection

```php
// Bind services
$container = new Container();
$container->bind('UserService', UserService::class);
$container->singleton('Database', Database::class);

// Resolve dependencies
$userService = $container->resolve('UserService');
```

### Custom Middleware

```php
class CustomMiddleware {
    public static function handle() {
        // Custom logic here
        if (!someCondition()) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
    }
}
```

### Error Handling

```php
// Global error handler automatically formats API errors
try {
    // Your code
} catch (Exception $e) {
    // Automatically returns JSON error response
    throw $e;
}
```

## Configuration

### Environment Variables

```php
// .env support (basic)
$_ENV['JWT_SECRET'] = 'your-secret-key';
$_ENV['APP_ENV'] = 'production';
```

### Database Configuration

```php
// app/core/config.php
'database' => [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'host' => 'localhost',
            'database' => 'your_db',
            'username' => 'your_user',
            'password' => 'your_password'
        ]
    ]
]
```

## Testing Examples

```bash
# Test API endpoints
curl -X GET "http://localhost:8000/api/users" \
  -H "Content-Type: application/json"

curl -X POST "http://localhost:8000/api/users" \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com"}'

curl -X GET "http://localhost:8000/api/auth/me" \
  -H "Authorization: Bearer <your-token>"
```

## Utils, Facades & Helpers

### Utility Classes

The framework includes several utility classes for common operations:

#### StringUtil

```php
use Core\Utils\StringUtil;

// String manipulation
StringUtil::camelCase('hello-world');        // 'helloWorld'
StringUtil::snakeCase('HelloWorld');         // 'hello_world'
StringUtil::kebabCase('HelloWorld');         // 'hello-world'
StringUtil::slug('Hello World PHP');         // 'hello-world-php'
StringUtil::truncate('Long text...', 10);    // 'Long text...'
StringUtil::uuid();                          // Generate UUID v4
StringUtil::random(16);                      // Random string
StringUtil::mask('1234567890', 4);          // '1234******'
```

#### ArrayUtil

```php
use Core\Utils\ArrayUtil;

// Array operations with dot notation
$data = ['user' => ['name' => 'John', 'email' => 'john@test.com']];

ArrayUtil::get($data, 'user.name');          // 'John'
ArrayUtil::set($data, 'user.age', 30);       // Set nested value
ArrayUtil::has($data, 'user.email');         // true
ArrayUtil::flatten([[1, 2], [3, 4]]);        // [1, 2, 3, 4]
ArrayUtil::only($arr, ['name', 'email']);    // Filter keys
ArrayUtil::pluck($users, 'email');           // Extract values
```

#### DateUtil

```php
use Core\Utils\DateUtil;

DateUtil::currentDate();                     // Current datetime
DateUtil::format('2024-01-01', 'd/m/Y');    // Format date
DateUtil::timeAgo('2024-01-01');            // 'X months ago'
DateUtil::addDays('2024-01-01', 7);         // Add days
DateUtil::diffInDays($date1, $date2);       // Days difference
DateUtil::toIso8601($date);                 // ISO format
```

#### FileUtil

```php
use Core\Utils\FileUtil;

FileUtil::get('path/to/file.txt');          // Read file
FileUtil::put('path/to/file.txt', 'data');  // Write file
FileUtil::delete('path/to/file.txt');       // Delete file
FileUtil::extension('file.php');            // 'php'
FileUtil::humanFileSize(1024000);           // '1000 KB'
FileUtil::makeDirectory('path/to/dir');     // Create dir
```

#### CryptoUtil

```php
use Core\Utils\CryptoUtil;

// Password hashing
$hash = CryptoUtil::hashPassword('secret');
CryptoUtil::verifyPassword('secret', $hash);

// Token generation
CryptoUtil::randomToken(32);                // Secure token
CryptoUtil::generateApiKey('sk', 32);       // API key

// Encryption
$encrypted = CryptoUtil::encrypt($data, $key);
$decrypted = CryptoUtil::decrypt($encrypted, $key);

// JWT
$token = CryptoUtil::generateJWT($payload, $secret);
$payload = CryptoUtil::verifyJWT($token, $secret);
```

### Facades

Facades provide a static interface to underlying services:

#### DB Facade

```php
use Core\Facades\DB;

// Raw queries
DB::query("SELECT * FROM users WHERE id = ?", [1]);

// Query builder
$users = DB::table('users')
    ->where('status', 'active')
    ->get();

// Transactions
DB::beginTransaction();
try {
    // ... operations
    DB::commit();
} catch (Exception $e) {
    DB::rollback();
}
```

#### Cache Facade

```php
use Core\Facades\Cache;

// Store data
Cache::put('key', 'value', 3600);  // TTL in seconds

// Retrieve data
$value = Cache::get('key', 'default');

// Check existence
if (Cache::has('key')) { }

// Remember (get or store)
$data = Cache::remember('key', function() {
    return expensiveOperation();
}, 3600);

// Remove data
Cache::forget('key');
Cache::flush();  // Clear all
```

#### Response Facade

```php
use Core\Facades\Response;

// JSON responses
Response::json(['data' => $data], 200);
Response::success($data, 'Success message');
Response::error('Error message', 400);

// Status-specific responses
Response::created($data);
Response::unauthorized('Unauthorized');
Response::forbidden('Forbidden');
Response::notFound('Not found');
Response::validationError($errors);

// File download
Response::download('/path/to/file.pdf', 'document.pdf');
```

#### Request Facade

```php
use Core\Facades\Request;

// Get input
$value = Request::get('key', 'default');
$all = Request::all();
$only = Request::only(['name', 'email']);

// Request information
Request::method();           // HTTP method
Request::isGet();           // Check method
Request::isAjax();          // Check if AJAX
Request::ip();              // Client IP
Request::userAgent();       // User agent
Request::header('X-Custom'); // Get header

// Files
if (Request::hasFile('avatar')) {
    $file = Request::file('avatar');
}
```

### Helper Functions

Global helper functions for convenience:

#### API Helpers

```php
// Response helpers
json_response(['data' => $data], 200);
success_response($data, 'Success');
error_response('Error', 400, $errors);
unauthorized('Unauthorized');
forbidden('Forbidden');
not_found('Not found');
validation_error($errors);

// Pagination
paginate_response($data, $total, $page, $perPage);

// CORS
cors_headers(['*'], ['GET', 'POST']);
```

#### String Helpers

```php
str_contains($haystack, $needle);    // Check substring
str_starts_with($str, $prefix);      // Check prefix
str_ends_with($str, $suffix);        // Check suffix
str_limit($str, 100, '...');         // Limit length
str_slug('Hello World');             // 'hello-world'
str_random(16);                      // Random string
str_camel('hello-world');            // 'helloWorld'
str_snake('HelloWorld');             // 'hello_world'
str_uuid();                          // Generate UUID
str_mask('1234567890', 4);          // '1234******'
```

#### Array Helpers

```php
array_get($arr, 'user.name', 'default');
array_set($arr, 'user.name', 'John');
array_has($arr, 'user.email');
array_only($arr, ['name', 'email']);
array_except($arr, ['password']);
array_pluck($users, 'email');
array_flatten([[1, 2], [3, 4]]);
array_first($arr, 'default');
array_last($arr, 'default');
```

#### HTTP Helpers

```php
request_method();                    // Get HTTP method
is_get();                           // Check if GET
is_post();                          // Check if POST
is_ajax();                          // Check if AJAX
is_json_request();                  // Check JSON content
get_json_input();                   // Get JSON body
get_header('X-Custom');             // Get header
get_bearer_token();                 // Extract Bearer token
client_ip();                        // Get client IP
current_url();                      // Get current URL
is_https();                         // Check HTTPS
base_url('/path');                  // Generate base URL
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

MIT License

## Comparison to NestJS

| Feature | NestJS | This Framework |
|---------|--------|----------------|
| Routing | Decorators | Fluent API |
| Validation | Class Validator | Rule-based |
| Middleware | Injectable | Static classes |
| DI Container | Advanced | Basic |
| Authentication | Guards | Middleware |
| Error Handling | Filters | Global handler |
| API Responses | Interceptors | Helper methods |
| CORS | Built-in | Middleware |

This framework brings many of the modern API development patterns found in NestJS to the PHP ecosystem while maintaining simplicity and performance.
