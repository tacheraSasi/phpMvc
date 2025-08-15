# PHP MVC Framework - Enhanced for Modern API Development

A modern, lightweight PHP MVC framework enhanced with powerful features for building REST APIs, similar to NestJS architecture patterns.

## Features

### 🚀 **Modern API Development**
- RESTful routing with parameter support
- JSON request/response handling
- CORS support out of the box
- Comprehensive validation system
- Standardized API response format

### 🔐 **Security & Authentication**  
- JWT authentication middleware
- SQL injection protection with prepared statements
- Rate limiting middleware
- Input validation and sanitization

### ⚡ **Developer Experience**
- Dependency injection container
- Middleware system for cross-cutting concerns
- Global error handling with debug mode
- Environment-aware configuration
- Fluent validation API

### 🛠 **Core Components**
- Enhanced routing system with HTTP verb support
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

```
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
│   └── middleware/      # Middleware components
│       ├── CorsMiddleware.php
│       ├── AuthMiddleware.php
│       ├── JsonMiddleware.php
│       └── RateLimitMiddleware.php
├── models/              # Data models
└── views/               # View templates (for web routes)
public/
├── index.php           # Application entry point
└── .htaccess          # URL rewriting rules
```

### Core Components

#### Enhanced Router

```php
// HTTP verbs support
$app->get('api/users', [UserApi::class, 'index']);
$app->post('api/users', [UserApi::class, 'store']);
$app->put('api/users/{id}', [UserApi::class, 'update']);
$app->delete('api/users/{id}', [UserApi::class, 'destroy']);

// RESTful resource routing
$app->resource('api/users', UserApi::class);

// Middleware application
$app->get('api/protected', [ProtectedController::class])
    ->middleware('auth');
```

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
| Routing | ✅ Decorators | ✅ Fluent API |
| Validation | ✅ Class Validator | ✅ Rule-based |
| Middleware | ✅ Injectable | ✅ Static classes |
| DI Container | ✅ Advanced | ✅ Basic |
| Authentication | ✅ Guards | ✅ Middleware |
| Error Handling | ✅ Filters | ✅ Global handler |
| API Responses | ✅ Interceptors | ✅ Helper methods |
| CORS | ✅ Built-in | ✅ Middleware |

This framework brings many of the modern API development patterns found in NestJS to the PHP ecosystem while maintaining simplicity and performance.
