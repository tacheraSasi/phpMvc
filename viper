<?php

if(php_sapi_name() !== 'cli')
{
	die("This tool is only for use in the command line");
}

define('DS', DIRECTORY_SEPARATOR);
define('CPATH', __DIR__.DS);
define('ROOTPATH', __DIR__.DS);

chdir(CPATH);

$action = $argv[1] ?? 'help';

require 'app'.DS.'core'.DS.'__init__.php';
require 'src'.DS.'Core'.DS.'Config.php';

// Load environment
\Viper\Core\Config::loadEnv();

// Enhanced Luna CLI
require 'app'.DS.'luna'.DS.'init.php';

$luna = new \Luna\Luna;

// Add new commands
if(empty($action))
{
	call_user_func_array([$luna,'help'], []);
}else
{
	$parts = explode(":", $action);
	$command = $parts[0];
	
	// Handle new viper commands
	if ($command === 'make') {
		$subCommand = $parts[1] ?? null;
		
		switch ($subCommand) {
			case 'controller':
				$luna->makeViperController($argv);
				break;
			case 'service':
				$luna->makeViperService($argv);
				break;
			case 'module':
				$luna->makeViperModule($argv);
				break;
			default:
				if(is_callable([$luna, $command]))
				{
					call_user_func_array([$luna, $command], [$argv]);
				} else {
					echo "\n\rThat command was not recognised. Please see below for commands\n\r";
					call_user_func_array([$luna,'help'], []);
				}
				break;
		}
	} else {
		if(is_callable([$luna, $command]))
		{
			call_user_func_array([$luna, $command], [$argv]);
		} else {
			echo "\n\rThat command was not recognised. Please see below for commands\n\r";
			call_user_func_array([$luna,'help'], []);
		}
	}
}