<?php 

session_start();
/**  Path to this file **/
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);

require "../app/core/__init__.php";

DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

$app = new App;
$request = new \Core\Request;

dump($request::get_uri());
$app->get('route/one',[Home::class,'index']);
$app->get('login',[Login::class]);
$app->post('login',[Login::class]);
$app->get('home/new',[Home::class,'index']);
#TODO:app->run() to run the app
#$app->loadController();
$app->run();


/*
TODO:fix the last slash issues home/
*/