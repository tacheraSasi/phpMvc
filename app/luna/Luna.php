<?php

namespace Luna;

defined('CPATH') OR exit('Access Denied!');

/**
 * Luna class
 */
class Luna
{

    private $version = '1.0.0';

    public function db($argv)
    {

        $mode    = $argv[1] ?? null;
        $param1  = $argv[2] ?? null;

        switch ($mode) {
            case 'db:create':

                /**check if param1 is empty**/
                if(empty($param1))
                    die("\n\rPlease provide a database name\n\r");

                $db = new Database;
                $query = "create database if not exists ". $param1;
                $db->query($query);

                die("\n\rDatabase created successfully\n\r");
                break;
            case 'db:table':

                /**check if param1 is empty**/
                if(empty($param1))
                    die("\n\rPlease provide a table name\n\r");

                $db = new Database;
                $query = "describe ". $param1;
                $res = $db->query($query);

                if($res)
                {

                    print_r($res);
                }else{
                    echo "\n\rCould not find data for table: $param1\n\r";
                }
                die();

                break;
            case 'db:drop':
                /**check if param1 is empty**/
                if(empty($param1))
                    die("\n\rPlease provide a database name\n\r");

                $db = new Database;
                $query = "drop database ". $param1;
                $db->query($query);

                die("\n\rDatabase deleted successfully\n\r");

                break;
            case 'db:seed':
                // code...
                break;
            
            default:
                die("\n\rUnknown command $argv[1]");
                break;
        }
    }

    public function make($argv)
    {
        $mode       = $argv[1] ?? null;
        $classname  = $argv[2] ?? null;

        /**check if class name is empty**/
        if(empty($classname))
            die("\n\rPlease provide a class name\n\r");

        /**clean class name **/
        $classname = preg_replace("/[^a-zA-Z0-9_]+/", "", $classname);
        
        /**check if class name starts with a number**/
        if(preg_match("/^[^a-zA-Z_]+/", $classname))
            die("\n\rClass names cant start with a number\n\r");

        switch ($mode) {
            case 'make:controller':

                $filename = 'app'.DS.'controllers'.DS.ucfirst($classname) . ".php";
                if(file_exists($filename))
                    die("\n\rThat controller already exists\n\r");
                
                $sample_file = file_get_contents('app'.DS.'Luna'.DS.'samples'.DS.'controller-sample.php');
                $sample_file = preg_replace("/\{CLASSNAME\}/", ucfirst($classname), $sample_file);
                $sample_file = preg_replace("/\{classname\}/", strtolower($classname), $sample_file);

                if(file_put_contents($filename, $sample_file))
                {
                    die("\n\rController created successfully\n\r");
                }else{
                    die("\n\rFailed to create Controller due to an error\n\r");
                }
                break;
            case 'make:model':

                $filename = 'app'.DS.'models'.DS.ucfirst($classname) . ".php";
                if(file_exists($filename))
                    die("\n\rThat model already exists\n\r");

                $sample_file = file_get_contents('app'.DS.'Luna'.DS.'samples'.DS.'model-sample.php');
                $sample_file = preg_replace("/\{CLASSNAME\}/", ucfirst($classname), $sample_file);
                
                /** only add as 's' at the end of table name if it doesnt exist**/
                if(!preg_match("/s$/", $classname))
                    $sample_file = preg_replace("/\{table\}/", strtolower($classname) . 's', $sample_file);

                if(file_put_contents($filename, $sample_file))
                {
                    die("\n\rModel created successfully\n\r");
                }else{
                    die("\n\rFailed to create Model due to an error\n\r");
                }
                break;
            case 'make:migration':
                // code...
                break;
            case 'make:seeder':
                // code...
                break;
            
            default:
                die("\n\rUnknown command $argv[1]");
                break;
        }
    }

    public function migrate()
    {
     echo "\n\rthis is the migrate function\n\r";
    }

    public function help()
    {
        echo "

    Luna v$this->version Command Line Tool

    Database
      db:create          Create a new database schema.
      db:seed            Runs the specified seeder to populate known data into the database.
      db:table           Retrieves information on the selected table.
      db:drop            Drop/Delete a database.
      migrate            Locates and runs a migration from the specified plugin folder.
      migrate:refresh    Does a rollback followed by a latest to refresh the current state of the database.
      migrate:rollback   Runs the 'down' method for a migration in the specifiled plugin folder.

    Generators
      make:controller    Generates a new controller file.
      make:model         Generates a new model file.
      make:migration     Generates a new migration file.
      make:seeder        Generates a new seeder file.

    Viper Framework (Enhanced)
      make:controller    Generates an enhanced controller in src/
      make:service       Generates a service class in src/
      make:module        Generates a module with controller and service
            
        ";
    }

    /**
     * Generate enhanced controller in src/ directory
     */
    public function makeViperController($argv)
    {
        $classname = $argv[2] ?? null;
        
        if(empty($classname)) {
            die("\n\rPlease provide a controller name\n\r");
        }
        
        $classname = preg_replace("/[^a-zA-Z0-9_]+/", "", $classname);
        
        if(preg_match("/^[^a-zA-Z_]+/", $classname)) {
            die("\n\rClass names cant start with a number\n\r");
        }
        
        if(!file_exists('src')) {
            mkdir('src', 0755, true);
        }
        
        if(!file_exists('src/Controllers')) {
            mkdir('src/Controllers', 0755, true);
        }
        
        $filename = 'src/Controllers/' . ucfirst($classname) . ".php";
        
        if(file_exists($filename)) {
            die("\n\rThat controller already exists\n\r");
        }
        
        $template = $this->getViperControllerTemplate();
        $template = str_replace('{CLASSNAME}', ucfirst($classname), $template);
        $template = str_replace('{classname}', strtolower($classname), $template);
        
        if(file_put_contents($filename, $template)) {
            echo "\n\rViper Controller created successfully: $filename\n\r";
        } else {
            die("\n\rFailed to create Controller due to an error\n\r");
        }
    }

    /**
     * Generate service in src/ directory
     */
    public function makeViperService($argv)
    {
        $classname = $argv[2] ?? null;
        
        if(empty($classname)) {
            die("\n\rPlease provide a service name\n\r");
        }
        
        $classname = preg_replace("/[^a-zA-Z0-9_]+/", "", $classname);
        
        if(preg_match("/^[^a-zA-Z_]+/", $classname)) {
            die("\n\rClass names cant start with a number\n\r");
        }
        
        if(!file_exists('src')) {
            mkdir('src', 0755, true);
        }
        
        if(!file_exists('src/Services')) {
            mkdir('src/Services', 0755, true);
        }
        
        $filename = 'src/Services/' . ucfirst($classname) . ".php";
        
        if(file_exists($filename)) {
            die("\n\rThat service already exists\n\r");
        }
        
        $template = $this->getViperServiceTemplate();
        $template = str_replace('{CLASSNAME}', ucfirst($classname), $template);
        
        if(file_put_contents($filename, $template)) {
            echo "\n\rViper Service created successfully: $filename\n\r";
        } else {
            die("\n\rFailed to create Service due to an error\n\r");
        }
    }

    /**
     * Generate module in src/ directory
     */
    public function makeViperModule($argv)
    {
        $modulename = $argv[2] ?? null;
        
        if(empty($modulename)) {
            die("\n\rPlease provide a module name\n\r");
        }
        
        $modulename = preg_replace("/[^a-zA-Z0-9_]+/", "", $modulename);
        
        if(preg_match("/^[^a-zA-Z_]+/", $modulename)) {
            die("\n\rModule names cant start with a number\n\r");
        }
        
        if(!file_exists('src')) {
            mkdir('src', 0755, true);
        }
        
        if(!file_exists('src/Modules')) {
            mkdir('src/Modules', 0755, true);
        }
        
        $moduleDir = 'src/Modules/' . ucfirst($modulename);
        
        if(file_exists($moduleDir)) {
            die("\n\rThat module already exists\n\r");
        }
        
        // Create module directory structure
        mkdir($moduleDir, 0755, true);
        mkdir($moduleDir . '/Controllers', 0755, true);
        mkdir($moduleDir . '/Services', 0755, true);
        
        // Create controller
        $controllerTemplate = $this->getViperControllerTemplate();
        $controllerTemplate = str_replace('{CLASSNAME}', ucfirst($modulename) . 'Controller', $controllerTemplate);
        $controllerTemplate = str_replace('{classname}', strtolower($modulename), $controllerTemplate);
        file_put_contents($moduleDir . '/Controllers/' . ucfirst($modulename) . 'Controller.php', $controllerTemplate);
        
        // Create service
        $serviceTemplate = $this->getViperServiceTemplate();
        $serviceTemplate = str_replace('{CLASSNAME}', ucfirst($modulename) . 'Service', $serviceTemplate);
        file_put_contents($moduleDir . '/Services/' . ucfirst($modulename) . 'Service.php', $serviceTemplate);
        
        // Create module file
        $moduleTemplate = $this->getViperModuleTemplate();
        $moduleTemplate = str_replace('{CLASSNAME}', ucfirst($modulename) . 'Module', $moduleTemplate);
        $moduleTemplate = str_replace('{MODULE}', ucfirst($modulename), $moduleTemplate);
        file_put_contents($moduleDir . '/' . ucfirst($modulename) . 'Module.php', $moduleTemplate);
        
        echo "\n\rViper Module created successfully: $moduleDir\n\r";
        echo "  - Controller: {$moduleDir}/Controllers/" . ucfirst($modulename) . "Controller.php\n\r";
        echo "  - Service: {$moduleDir}/Services/" . ucfirst($modulename) . "Service.php\n\r";
        echo "  - Module: {$moduleDir}/" . ucfirst($modulename) . "Module.php\n\r";
    }

    /**
     * Get Viper controller template
     */
    protected function getViperControllerTemplate(): string
    {
        return '<?php

namespace App\Controllers;

use Viper\Http\Request;
use Viper\Http\Response;

/**
 * {CLASSNAME} Controller
 */
class {CLASSNAME}
{
    /**
     * Display a listing of the resource
     */
    public function index(Request $request, Response $response): Response
    {
        return $response->json([
            \'message\' => \'Hello from {CLASSNAME}!\',
            \'data\' => []
        ]);
    }

    /**
     * Show the form for creating a new resource
     */
    public function create(Request $request, Response $response): Response
    {
        return $response->json([\'message\' => \'Create form for {classname}\']);
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->all();
        
        // TODO: Implement validation and storage logic
        
        return $response->json([
            \'message\' => \'{CLASSNAME} created successfully\',
            \'data\' => $data
        ], 201);
    }

    /**
     * Display the specified resource
     */
    public function show(Request $request, Response $response): Response
    {
        $id = $request->query(\'id\');
        
        return $response->json([
            \'message\' => \'Showing {classname} with ID: \' . $id,
            \'data\' => [\'id\' => $id]
        ]);
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(Request $request, Response $response): Response
    {
        $id = $request->query(\'id\');
        
        return $response->json([\'message\' => \'Edit form for {classname} ID: \' . $id]);
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, Response $response): Response
    {
        $id = $request->query(\'id\');
        $data = $request->all();
        
        // TODO: Implement validation and update logic
        
        return $response->json([
            \'message\' => \'{CLASSNAME} updated successfully\',
            \'data\' => array_merge([\'id\' => $id], $data)
        ]);
    }

    /**
     * Remove the specified resource
     */
    public function destroy(Request $request, Response $response): Response
    {
        $id = $request->query(\'id\');
        
        // TODO: Implement deletion logic
        
        return $response->json([
            \'message\' => \'{CLASSNAME} deleted successfully\',
            \'data\' => [\'id\' => $id]
        ]);
    }
}
';
    }

    /**
     * Get Viper service template
     */
    protected function getViperServiceTemplate(): string
    {
        return '<?php

namespace App\Services;

use Viper\Services\BaseService;

/**
 * {CLASSNAME} Service
 */
class {CLASSNAME} extends BaseService
{
    /**
     * Get all items
     */
    public function getAll(): array
    {
        // TODO: Implement business logic
        return [];
    }

    /**
     * Get item by ID
     */
    public function getById(int $id): ?array
    {
        // TODO: Implement business logic
        return null;
    }

    /**
     * Create new item
     */
    public function create(array $data): array
    {
        // TODO: Implement creation logic
        return $data;
    }

    /**
     * Update existing item
     */
    public function update(int $id, array $data): array
    {
        // TODO: Implement update logic
        return array_merge([\'id\' => $id], $data);
    }

    /**
     * Delete item
     */
    public function delete(int $id): bool
    {
        // TODO: Implement deletion logic
        return true;
    }
}
';
    }

    /**
     * Get Viper module template
     */
    protected function getViperModuleTemplate(): string
    {
        return '<?php

namespace App\Modules\{MODULE};

use Viper\Core\Router;
use Viper\Core\Container;
use App\Modules\{MODULE}\Controllers\{MODULE}Controller;
use App\Modules\{MODULE}\Services\{MODULE}Service;

/**
 * {CLASSNAME} Module
 */
class {CLASSNAME}
{
    /**
     * Register module routes
     */
    public function registerRoutes(Router $router): void
    {
        $router->get(\'/{classname}\', [{MODULE}Controller::class, \'index\']);
        $router->post(\'/{classname}\', [{MODULE}Controller::class, \'store\']);
        $router->get(\'/{classname}/create\', [{MODULE}Controller::class, \'create\']);
        $router->get(\'/{classname}/{{id}}\', [{MODULE}Controller::class, \'show\']);
        $router->get(\'/{classname}/{{id}}/edit\', [{MODULE}Controller::class, \'edit\']);
        $router->put(\'/{classname}/{{id}}\', [{MODULE}Controller::class, \'update\']);
        $router->delete(\'/{classname}/{{id}}\', [{MODULE}Controller::class, \'destroy\']);
    }

    /**
     * Register module services
     */
    public function registerServices(Container $container): void
    {
        $container->bind({MODULE}Service::class);
    }

    /**
     * Boot module
     */
    public function boot(): void
    {
        // Module initialization logic
    }
}
';
    }
}