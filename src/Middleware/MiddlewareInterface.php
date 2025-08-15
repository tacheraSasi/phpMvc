<?php

namespace Viper\Middleware;

use Viper\Http\Request;
use Viper\Http\Response;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Middleware interface
 */
interface MiddlewareInterface
{
    /**
     * Handle the middleware
     */
    public function handle(Request $request, callable $next): Response;
}