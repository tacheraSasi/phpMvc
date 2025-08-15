# Enhanced phpMvc Framework - Usage Examples

## Quick Start

1. **Environment Setup**
```bash
cp .env.example .env
# Edit .env with your configuration
```

2. **Generate Components**
```bash
php viper make:controller UserController
php viper make:service UserService  
php viper make:module AuthModule
```

3. **Run the Enhanced Application**
```bash
cd public
php -S localhost:8000
# Visit: http://localhost:8000/enhanced.php?url=home
```

## Example Usage

### 1. Basic Routes with Middleware
```php
// Enhanced framework usage
$app = new \Viper\Core\Application();

// Routes with middleware
$app->get('/api/users', [UserController::class, 'index'])
    ->middleware(['cors', 'rate-limit']);

$app->post('/api/users', [UserController::class, 'store'])
    ->middleware(['cors', 'rate-limit']);
```

### 2. Request Validation
```php
$app->post('/api/validate', function(\Viper\Http\Request $request, \Viper\Http\Response $response) {
    $validator = \Viper\Validation\Validator::make($request->all(), [
        'name' => 'required|alpha|min:2',
        'email' => 'required|email',
        'age' => 'numeric|min:18'
    ]);
    
    if ($validator->fails()) {
        return $response->json([
            'error' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }
    
    return $response->json(['message' => 'Validation passed']);
});
```

### 3. Enhanced Controllers
```php
namespace App\Controllers;

use Viper\Http\Request;
use Viper\Http\Response;

class UserController
{
    public function index(Request $request, Response $response): Response
    {
        return $response->json([
            'users' => ['John', 'Jane'],
            'total' => 2
        ]);
    }
    
    public function store(Request $request, Response $response): Response
    {
        $data = $request->all();
        // Process data...
        return $response->json(['created' => $data], 201);
    }
}
```

### 4. Services with Dependency Injection
```php
namespace App\Services;

class UserService extends \Viper\Services\BaseService
{
    public function getAll(): array
    {
        // Business logic here
        return ['users' => []];
    }
    
    public function create(array $data): array
    {
        // Validation and creation logic
        return $data;
    }
}
```

### 5. Custom Middleware
```php
namespace App\Middleware;

use Viper\Middleware\MiddlewareInterface;
use Viper\Http\Request;
use Viper\Http\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $token = $request->header('Authorization');
        
        if (!$token) {
            return Response::error('Unauthorized', 401);
        }
        
        return $next($request);
    }
}
```

### 6. Module System
```php
namespace App\Modules\User;

use Viper\Core\Router;
use Viper\Core\Container;

class UserModule
{
    public function registerRoutes(Router $router): void
    {
        $router->get('/users', [UserController::class, 'index']);
        $router->post('/users', [UserController::class, 'store']);
    }
    
    public function registerServices(Container $container): void
    {
        $container->bind(UserService::class);
    }
}
```

## Testing the Features

### 1. Test Basic Endpoints
```bash
# Home page
curl http://localhost:8000/enhanced.php?url=home

# Health check
curl http://localhost:8000/enhanced.php?url=health
```

### 2. Test Validation
```bash
# Valid data
curl -X POST -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com","age":"25"}' \
  http://localhost:8000/enhanced.php?url=api/validate

# Invalid data  
curl -X POST -H "Content-Type: application/json" \
  -d '{"name":"J","email":"invalid","age":"17"}' \
  http://localhost:8000/enhanced.php?url=api/validate
```

### 3. Check Logs
```bash
tail -f storage/logs/app-$(date +%Y-%m-%d).log
```

## Configuration

### Environment Variables (.env)
```env
APP_NAME="Enhanced phpMvc Framework"
APP_ENV=development
APP_DEBUG=true

# CORS Configuration
CORS_ENABLED=true
CORS_ALLOWED_ORIGINS=*

# Rate Limiting
RATE_LIMIT_ENABLED=true
RATE_LIMIT_REQUESTS=60
RATE_LIMIT_WINDOW=60

# Logging
LOG_CHANNEL=file
LOG_LEVEL=debug
```

## CLI Commands

```bash
# Generate controller
php viper make:controller ProductController

# Generate service
php viper make:service ProductService

# Generate complete module
php viper make:module ProductModule

# Legacy commands still work
php luna make:controller OldController
php luna make:model User
```

The enhanced framework maintains backward compatibility while adding modern features like middleware, dependency injection, validation, and comprehensive logging.