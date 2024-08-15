<?php 

session_start();

define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
require "../app/core/__init__.php";
DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);
/* ---------------------------------------------------------------- */

$app = new App;

#routes
$app->get('luna',[Luna::class]);
$app->get('luna/render',[Luna::class,'render']);

$app->get('login',[Login::class,'index']);
$app->post('login',[Login::class,'index']);
$app->get('new',[Home::class,'new']);
$app->get('todo',[TodoApp::class]);
$app->get('todo/new',[TodoApp::class, 'new']);
$app->post('todo/new',[TodoApp::class, 'add']);

#lunaPHP will run 
#Through the run method
$app->run();


/*
TODO:fix the last slash issues home/
database and model
*/