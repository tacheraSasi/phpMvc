<?php 

use Controller\Api;
use Controller\AuthApi;
use Controller\Home;
use Controller\Login;
use Controller\Luna;
use Controller\ProtectedApi;
use Controller\TodoApp;
use Controller\UserApi;

session_start();

define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
require "../app/core/__init__.php";
DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

// Load middleware classes
require_once "../app/core/middleware/CorsMiddleware.php";
require_once "../app/core/middleware/JsonMiddleware.php";
require_once "../app/core/middleware/RateLimitMiddleware.php";
require_once "../app/core/middleware/AuthMiddleware.php";

// Apply global middleware
\Core\Middleware\CorsMiddleware::handle();
\Core\Middleware\JsonMiddleware::handle();
\Core\Middleware\RateLimitMiddleware::handle();

/* ---------------------------------------------------------------- */


// Register routes using static methods
App::get('static-home',[Home::class]);
App::get('static-luna',[Luna::class]);
App::get('static-luna/render',[Luna::class,'render']);
App::get('static-login',[Login::class,'index']);
App::post('static-login',[Login::class,'index']);
App::get('static-new',[Home::class,'new']);
App::get('static-todo',[TodoApp::class]);
App::get('static-todo/new',[TodoApp::class, 'new']);
App::post('static-todo/new',[TodoApp::class, 'add']);
App::get('static-api',[Api::class]);
App::post('static-api/auth/login',[AuthApi::class, 'login']);
App::post('static-api/auth/register',[AuthApi::class, 'register']);
App::get('static-api/auth/me',[AuthApi::class, 'me']);
App::post('static-api/auth/refresh',[AuthApi::class, 'refresh']);
App::get('static-api/profile',[ProtectedApi::class, 'profile']);
App::get('static-api/dashboard',[ProtectedApi::class, 'dashboard']);
App::resource('static-api/users', UserApi::class);
App::get('static-api/users',[UserApi::class, 'index']);
App::post('static-api/users',[UserApi::class, 'store']);
App::get('static-api/users/{id}',[UserApi::class, 'show']);
App::put('static-api/users/{id}',[UserApi::class, 'update']);
App::delete('static-api/users/{id}',[UserApi::class, 'destroy']);
App::get('static-api/{any}',[Api::class, 'notFound']);

// Register routes using instance methods
$app = new App;
$app->get('home',[Home::class]);
$app->get('luna',[Luna::class]);
$app->get('luna/render',[Luna::class,'render']);
$app->get('login',[Login::class,'index']);
$app->post('login',[Login::class,'index']);
$app->get('new',[Home::class,'new']);
$app->get('todo',[TodoApp::class]);
$app->get('todo/new',[TodoApp::class, 'new']);
$app->post('todo/new',[TodoApp::class, 'add']);
$app->get('api',[Api::class]);
$app->post('api/auth/login',[AuthApi::class, 'login']);
$app->post('api/auth/register',[AuthApi::class, 'register']);
$app->get('api/auth/me',[AuthApi::class, 'me']);
$app->post('api/auth/refresh',[AuthApi::class, 'refresh']);
$app->get('api/profile',[ProtectedApi::class, 'profile']);
$app->get('api/dashboard',[ProtectedApi::class, 'dashboard']);
$app->resource('api/users', UserApi::class);
$app->get('api/users',[UserApi::class, 'index']);
$app->post('api/users',[UserApi::class, 'store']);
$app->get('api/users/{id}',[UserApi::class, 'show']);
$app->put('api/users/{id}',[UserApi::class, 'update']);
$app->delete('api/users/{id}',[UserApi::class, 'destroy']);
$app->get('api/{any}',[Api::class, 'notFound']);

$app->run();


/*
TODO:fix the last slash issues home/
database and model
*/