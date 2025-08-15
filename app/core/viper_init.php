<?php

defined('ROOTPATH') OR exit('Access Denied!');

// Enhanced autoloader for Viper framework
spl_autoload_register(function($classname) {
    $parts = explode("\\", $classname);
    
    // Handle Viper namespace
    if ($parts[0] === 'Viper') {
        $path = "../src/" . implode("/", array_slice($parts, 1)) . ".php";
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
    
    // Handle App namespace (for generated code)
    if ($parts[0] === 'App') {
        $path = "../src/" . implode("/", array_slice($parts, 1)) . ".php";
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
    
    // Handle legacy Controller namespace
    if ($parts[0] === 'Controller') {
        $className = end($parts);
        $path = "../app/controllers/" . $className . ".php";
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
    
    // Handle legacy Model namespace
    if ($parts[0] === 'Model') {
        $className = end($parts);
        $path = "../app/models/" . $className . ".php";
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
    
    // Handle legacy Luna namespace
    if ($parts[0] === 'Luna') {
        $className = end($parts);
        $path = "../app/models/" . $className . ".php";
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
    
    // Fallback to original autoloader behavior
    $classname = end($parts);
    $modelPath = "../app/models/" . ucfirst($classname) . ".php";
    if (file_exists($modelPath)) {
        require $modelPath;
        return;
    }
});

/** Valid PHP Version? **/
$minPHPVersion = '8.0';
if (phpversion() < $minPHPVersion) {
    die("Your PHP version must be {$minPHPVersion} or higher to run this app. Your current version is " . phpversion());
}

// Load core files
require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'App.php';

// Load enhanced framework
require '../src/Core/Config.php';

// Auto-load environment
\Viper\Core\Config::loadEnv();

// Load other Viper core files
require '../src/Core/Container.php';
require '../src/Core/Application.php';