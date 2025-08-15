<?php

namespace Viper\Core;

use ReflectionClass;
use ReflectionParameter;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Simple Dependency Injection Container
 */
class Container
{
    protected array $bindings = [];
    protected array $instances = [];
    protected array $singletons = [];
    protected static ?Container $instance = null;
    
    /**
     * Get container instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Bind a service to the container
     */
    public function bind(string $abstract, callable|string $concrete = null, bool $singleton = false): void
    {
        $concrete = $concrete ?? $abstract;
        
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton
        ];
        
        if ($singleton) {
            $this->singletons[$abstract] = false;
        }
    }
    
    /**
     * Bind a singleton to the container
     */
    public function singleton(string $abstract, callable|string $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }
    
    /**
     * Bind an instance to the container
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }
    
    /**
     * Resolve a service from the container
     */
    public function resolve(string $abstract): mixed
    {
        // Check if we have a bound instance
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        // Check if it's a singleton that's already been resolved
        if (isset($this->singletons[$abstract]) && $this->singletons[$abstract] !== false) {
            return $this->singletons[$abstract];
        }
        
        // Resolve the binding
        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            $concrete = $binding['concrete'];
            
            if (is_callable($concrete)) {
                $instance = $concrete($this);
            } else {
                $instance = $this->build($concrete);
            }
            
            // Store singleton instance
            if ($binding['singleton']) {
                $this->singletons[$abstract] = $instance;
            }
            
            return $instance;
        }
        
        // Try to auto-resolve
        return $this->build($abstract);
    }
    
    /**
     * Build a class with automatic dependency injection
     */
    public function build(string $className): object
    {
        if (!class_exists($className)) {
            throw new \Exception("Class $className not found");
        }
        
        $reflectionClass = new ReflectionClass($className);
        
        if (!$reflectionClass->isInstantiable()) {
            throw new \Exception("Class $className is not instantiable");
        }
        
        $constructor = $reflectionClass->getConstructor();
        
        if ($constructor === null) {
            return new $className();
        }
        
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            $dependency = $this->resolveDependency($parameter);
            $dependencies[] = $dependency;
        }
        
        return $reflectionClass->newInstanceArgs($dependencies);
    }
    
    /**
     * Resolve a single dependency
     */
    protected function resolveDependency(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();
        
        if ($type === null) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            
            throw new \Exception("Cannot resolve parameter {$parameter->getName()}");
        }
        
        if ($type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            
            throw new \Exception("Cannot resolve built-in parameter {$parameter->getName()}");
        }
        
        $className = $type->getName();
        
        try {
            return $this->resolve($className);
        } catch (\Exception $e) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            
            if ($parameter->allowsNull()) {
                return null;
            }
            
            throw $e;
        }
    }
    
    /**
     * Check if a service is bound
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }
    
    /**
     * Get all bindings
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
    
    /**
     * Clear all bindings and instances
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->singletons = [];
    }
}