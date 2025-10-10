# Callback Routes in PHP MVC Framework

The PHP MVC Framework now supports callback functions as route handlers, providing a more flexible and concise way to define routes alongside the traditional controller-based approach.

## Table of Contents
- [Overview](#overview)
- [Basic Usage](#basic-usage)
- [Callback Parameters](#callback-parameters)
- [Examples](#examples)
- [HTTP Methods](#http-methods)
- [Route Parameters](#route-parameters)
- [Backward Compatibility](#backward-compatibility)

## Overview

Callback routes allow you to define route handlers inline using anonymous functions (closures), similar to popular frameworks like Express.js and Laravel. This is particularly useful for:
- Simple API endpoints
- Quick prototyping
- Middleware-like functionality
- Routes that don't require a full controller

## Basic Usage

### Simple GET Route

```php
App::get('hello', function($req, $res) {
    $res::json(['message' => 'Hello World!']);
});
```

### Instance Method

```php
$app = new App;
$app->get('hello', function($req, $res) {
    echo json_encode(['message' => 'Hello World!']);
});
$app->run();
```

## Callback Parameters

Callback functions receive two parameters by default:

1. **$req** - Request object (`\Luna\Request` instance)
   - Access request data: `$req->post('key')`, `$req->get('key')`
   - Get request method: `$req::method()`
   - Get URI: `$req::get_uri()`
   - Get all input: `$req->allInput()`

2. **$res** - Response object (`\Core\Facades\Response` instance)
   - Send JSON: `$res::json(['data' => $value])`
   - Send success: `$res::success($data, 'Message')`
   - Send error: `$res::error('Message', 400)`
   - Redirect: `$res::redirect('/path')`

## Examples

### 1. Simple JSON Response

```php
App::get('api/status', function($req, $res) {
    $res::json([
        'status' => 'online',
        'timestamp' => time()
    ]);
});
```

### 2. POST Request Handler

```php
App::post('api/users', function($req, $res) {
    $name = $req->post('name');
    $email = $req->post('email');
    
    // Process data...
    
    $res::success([
        'name' => $name,
        'email' => $email
    ], 'User created successfully');
});
```

### 3. Accessing Request Data

```php
App::get('api/search', function($req, $res) {
    $query = $req->get('q');
    $page = $req->get('page', 1);
    
    $res::json([
        'query' => $query,
        'page' => $page,
        'results' => []
    ]);
});
```

### 4. Form Data Processing

```php
App::post('contact', function($req, $res) {
    $name = $req->post('name');
    $message = $req->post('message');
    
    // Send email...
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting us!'
    ]);
});
```

## HTTP Methods

All standard HTTP methods support callbacks:

```php
// GET
App::get('route', function($req, $res) { /* ... */ });

// POST
App::post('route', function($req, $res) { /* ... */ });

// PUT
App::put('route', function($req, $res) { /* ... */ });

// PATCH
App::patch('route', function($req, $res) { /* ... */ });

// DELETE
App::delete('route', function($req, $res) { /* ... */ });

// OPTIONS
App::options('route', function($req, $res) { /* ... */ });
```

Instance methods also support callbacks:

```php
$app = new App;
$app->get('route', function($req, $res) { /* ... */ });
$app->post('route', function($req, $res) { /* ... */ });
$app->put('route', function($req, $res) { /* ... */ });
$app->delete('route', function($req, $res) { /* ... */ });
$app->run();
```

## Route Parameters

Callbacks can accept route parameters just like controller methods:

### Single Parameter

```php
App::get('user/{id}', function($req, $res, $id) {
    $res::json([
        'user_id' => $id,
        'message' => "Fetching user $id"
    ]);
});
```

### Multiple Parameters

```php
App::get('posts/{category}/{id}', function($req, $res, $category, $id) {
    $res::json([
        'category' => $category,
        'post_id' => $id
    ]);
});
```

### Example with Data Processing

```php
App::get('api/users/{id}', function($req, $res, $id) {
    // Fetch user from database
    $user = User::find($id);
    
    if ($user) {
        $res::success($user, 'User found');
    } else {
        $res::notFound('User not found');
    }
});
```

```php
App::put('api/users/{id}', function($req, $res, $id) {
    $name = $req->input('name');
    $email = $req->input('email');
    
    // Update user...
    
    $res::success([
        'id' => $id,
        'name' => $name,
        'email' => $email
    ], 'User updated');
});
```

```php
App::delete('api/users/{id}', function($req, $res, $id) {
    // Delete user...
    
    $res::success(null, "User $id deleted");
});
```

## Backward Compatibility

**Important:** The traditional controller-based routing still works exactly as before. You can mix both styles:

```php
use Controller\Home;
use Controller\UserApi;

// Traditional controller routes
App::get('home', [Home::class]);
App::get('users', [UserApi::class, 'index']);

// New callback routes
App::get('quick-api', function($req, $res) {
    $res::json(['status' => 'ok']);
});

// Both work together!
$app = new App;
$app->run();
```

## Complete Example

Here's a complete example showing various callback route patterns:

```php
<?php

use Controller\Home;

session_start();
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
require "../app/core/__init__.php";

// Simple routes
App::get('api/health', function($req, $res) {
    $res::json(['status' => 'healthy', 'timestamp' => time()]);
});

// Parameterized routes
App::get('api/users/{id}', function($req, $res, $id) {
    $res::json(['user_id' => $id, 'name' => 'John Doe']);
});

// POST with data
App::post('api/login', function($req, $res) {
    $username = $req->post('username');
    $password = $req->post('password');
    
    // Authenticate...
    
    $res::success(['token' => 'abc123'], 'Login successful');
});

// Multiple parameters
App::get('blog/{year}/{month}/{slug}', function($req, $res, $year, $month, $slug) {
    $res::json([
        'year' => $year,
        'month' => $month,
        'slug' => $slug
    ]);
});

// Traditional controller route (still works!)
$app = new App;
$app->get('home', [Home::class]);

// Instance callback
$app->post('api/contact', function($req, $res) {
    $message = $req->post('message');
    $res::success(['received' => $message], 'Message received');
});

$app->run();
```

## Tips and Best Practices

1. **Use callbacks for simple routes**: If your logic fits in a few lines, a callback is perfect.
2. **Use controllers for complex logic**: If you need multiple methods or complex business logic, use a controller class.
3. **Mix both approaches**: Use callbacks for API endpoints and controllers for page rendering.
4. **Type hints work**: Your callback will receive the correct Request and Response objects.
5. **Parameters order matters**: Always `function($req, $res, ...params)` - request and response first, then route parameters.

## Migration from Controllers

If you have a simple controller:

```php
// Before
class QuickApi {
    public function status() {
        echo json_encode(['status' => 'ok']);
    }
}
App::get('status', [QuickApi::class, 'status']);
```

You can simplify it to:

```php
// After
App::get('status', function($req, $res) {
    $res::json(['status' => 'ok']);
});
```

## Conclusion

Callback routes provide a modern, flexible approach to defining routes while maintaining full backward compatibility with the existing controller-based system. Choose the approach that best fits your use case!
