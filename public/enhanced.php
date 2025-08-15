<?php 

session_start();

define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
require "../app/core/viper_init.php";

// Create enhanced Viper application
$app = new \Viper\Core\Application();

// Example of creating middleware and routes with the enhanced framework

// Register some middleware aliases for convenience
$app->getRouter()->aliasMiddleware('auth', 'App\\Middleware\\AuthMiddleware');

// API Routes with middleware
$app->get('/api/users', [\App\Controllers\UserController::class, 'index'])
    ->middleware(['cors', 'rate-limit']);

$app->post('/api/users', [\App\Controllers\UserController::class, 'store'])
    ->middleware(['cors', 'rate-limit']);

$app->get('/api/users/{id}', [\App\Controllers\UserController::class, 'show'])
    ->middleware(['cors']);

// Example validation endpoint
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
    
    return $response->json([
        'message' => 'Validation passed',
        'data' => $request->all()
    ]);
})->middleware(['cors']);

// Legacy routes for backward compatibility
$app->get('luna', [Luna::class]);
$app->get('luna/render', [Luna::class, 'render']);
$app->get('login', [Login::class, 'index']);
$app->post('login', [Login::class, 'index']);
$app->get('new', [Home::class, 'new']);
$app->get('todo', [TodoApp::class]);
$app->get('todo/new', [TodoApp::class, 'new']);
$app->post('todo/new', [TodoApp::class, 'add']);

// Default home route
$app->get('home', function(\Viper\Http\Request $request, \Viper\Http\Response $response) {
    return $response->json([
        'message' => 'Welcome to Enhanced phpMvc Framework!',
        'framework' => 'Viper',
        'version' => '1.0.0',
        'features' => [
            'Middleware System',
            'Request/Response Wrappers', 
            'Error Handling',
            'Environment Config',
            'Dependency Injection',
            'Validation System',
            'Rate Limiting',
            'CORS Support',
            'Logging'
        ]
    ]);
});

// Health check endpoint
$app->get('health', function(\Viper\Http\Request $request, \Viper\Http\Response $response) {
    return $response->json([
        'status' => 'ok',
        'timestamp' => date('Y-m-d H:i:s'),
        'memory_usage' => memory_get_usage(true),
        'config_loaded' => \Viper\Core\Config::has('APP_NAME')
    ]);
});

// Run the enhanced application
$app->run();