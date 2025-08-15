<?php 

session_start();

define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
require "../app/core/__init__.php";
DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

// Load middleware classes
require_once "../app/core/middleware/CorsMiddleware.php";
require_once "../app/core/middleware/JsonMiddleware.php";

// Apply global middleware
\Core\Middleware\CorsMiddleware::handle();
\Core\Middleware\JsonMiddleware::handle();

/* ---------------------------------------------------------------- */

$app = new App;

// Web routes
$app->get('home',[Home::class]);
$app->get('luna',[Luna::class]);
$app->get('luna/render',[Luna::class,'render']);
$app->get('login',[Login::class,'index']);
$app->post('login',[Login::class,'index']);
$app->get('new',[Home::class,'new']);
$app->get('todo',[TodoApp::class]);
$app->get('todo/new',[TodoApp::class, 'new']);
$app->post('todo/new',[TodoApp::class, 'add']);

// API routes
$app->get('api',[Api::class]);
$app->resource('api/users', UserApi::class);

// Alternative explicit API routes for demonstration
$app->get('api/users',[UserApi::class, 'index']);
$app->post('api/users',[UserApi::class, 'store']);
$app->get('api/users/{id}',[UserApi::class, 'show']);
$app->put('api/users/{id}',[UserApi::class, 'update']);
$app->delete('api/users/{id}',[UserApi::class, 'destroy']);

// Fallback for undefined API routes
$app->get('api/{any}',[Api::class, 'notFound']);

#lunaPHP will run 
#Through the run method
$app->run();


/*
TODO:fix the last slash issues home/
database and model
*/