<?php

defined('ROOTPATH') or exit('Access Denied!');

// Define application constants if not already defined
if (!defined('DEBUG')) {
	define('DEBUG', true); //FIXME: Set to false for production
}

if (!defined('APP_NAME')) {
	define('APP_NAME', 'PHP MVC Framework');
}

if (!defined('APP_DESC')) {
	define('APP_DESC', 'Modern PHP MVC Framework for APIs');
}

if (!defined('DBHOST')) {
	define('DBHOST', 'localhost');
}

if (!defined('DBNAME')) {
	define('DBNAME', 'mvc_db');
}

if (!defined('DBUSER')) {
	define('DBUSER', 'root');
}

if (!defined('DBPASS')) {
	define('DBPASS', '');
}

if (!defined('DBDRIVER')) {
	define('DBDRIVER', 'mysql');
}

spl_autoload_register(function ($classname) {
	$classname = explode("\\", $classname);
	$classname = end($classname);

	// Try models directory first
	$modelFile = "../app/models/" . ucfirst($classname) . ".php";
	if (file_exists($modelFile)) {
		require $modelFile;
		return;
	}

	// Try core directory
	$coreFile = "../app/core/" . ucfirst($classname) . ".php";
	if (file_exists($coreFile)) {
		require $coreFile;
		return;
	}
	
	// Try traits directory (for traits)
	$traitFile = "../app/core/traits/".lcfirst($classname).".php";
	if(file_exists($traitFile)) {
		require $traitFile;
		return;
	}
});

/**  Valid PHP Version? **/
$minPHPVersion = '8.0';
if (phpversion() < $minPHPVersion) {
	die("Your PHP version must be {$minPHPVersion} or higher to run this app. Your current version is " . phpversion());
}

require 'config.php';
require 'functions.php';

// Load helper trait first
require 'traits/helper.php';

require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'Router.php';
require 'ErrorHandler.php';
require 'Container.php';
require 'Config.php';
require 'Validator.php';
require 'App.php';

// Load utility classes
require 'utils/StringUtil.php';
require 'utils/ArrayUtil.php';
require 'utils/DateUtil.php';
require 'utils/FileUtil.php';
require 'utils/CryptoUtil.php';

// Load facades
require 'facades/Facade.php';
require 'facades/DB.php';
require 'facades/Cache.php';
require 'facades/Response.php';
require 'facades/Request.php';
require 'facades/Validator.php';

// Load helper functions
require 'helpers/api_helpers.php';
require 'helpers/array_helpers.php';
require 'helpers/string_helpers.php';
require 'helpers/http_helpers.php';

// Register error handler for better API error responses
ErrorHandler::register();
