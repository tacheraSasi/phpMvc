<?php 

defined('ROOTPATH') OR exit('Access Denied!');

spl_autoload_register(function($classname){
	$classname = explode("\\", $classname);
	$classname = end($classname);
	
	// Try models directory first
	$modelFile = "../app/models/".ucfirst($classname).".php";
	if(file_exists($modelFile)) {
		require $modelFile;
		return;
	}
	
	// Try core directory
	$coreFile = "../app/core/".ucfirst($classname).".php";
	if(file_exists($coreFile)) {
		require $coreFile;
		return;
	}
});

/**  Valid PHP Version? **/
$minPHPVersion = '8.0';
if (phpversion() < $minPHPVersion)
{
	die("Your PHP version must be {$minPHPVersion} or higher to run this app. Your current version is " . phpversion());
}

require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'Router.php';
require 'ErrorHandler.php';
require 'Container.php';
require 'Config.php';
require 'Validator.php';
require 'App.php';

// Register error handler for better API error responses
ErrorHandler::register();