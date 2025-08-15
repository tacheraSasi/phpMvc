<?php

namespace App\Modules\ProductModule;

use Viper\Core\Router;
use Viper\Core\Container;
use App\Modules\ProductModule\Controllers\ProductModuleController;
use App\Modules\ProductModule\Services\ProductModuleService;

/**
 * ProductModuleModule Module
 */
class ProductModuleModule
{
    /**
     * Register module routes
     */
    public function registerRoutes(Router $router): void
    {
        $router->get('/{classname}', [ProductModuleController::class, 'index']);
        $router->post('/{classname}', [ProductModuleController::class, 'store']);
        $router->get('/{classname}/create', [ProductModuleController::class, 'create']);
        $router->get('/{classname}/{{id}}', [ProductModuleController::class, 'show']);
        $router->get('/{classname}/{{id}}/edit', [ProductModuleController::class, 'edit']);
        $router->put('/{classname}/{{id}}', [ProductModuleController::class, 'update']);
        $router->delete('/{classname}/{{id}}', [ProductModuleController::class, 'destroy']);
    }

    /**
     * Register module services
     */
    public function registerServices(Container $container): void
    {
        $container->bind(ProductModuleService::class);
    }

    /**
     * Boot module
     */
    public function boot(): void
    {
        // Module initialization logic
    }
}
