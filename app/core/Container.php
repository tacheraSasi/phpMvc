<?php

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Simple Dependency Injection Container
 */
class Container
{
    protected $bindings = [];
    protected $instances = [];

    public function bind($abstract, $concrete = null)
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null;
    }

    public function resolve($abstract)
    {
        // If it's already resolved as singleton, return the instance
        if (isset($this->instances[$abstract]) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // If not bound, try to resolve by class name
        if (!isset($this->bindings[$abstract])) {
            return $this->build($abstract);
        }

        $concrete = $this->bindings[$abstract];

        // If it's a closure, execute it
        if ($concrete instanceof Closure) {
            $object = $concrete($this);
        } else {
            $object = $this->build($concrete);
        }

        // If it's a singleton, store the instance
        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    protected function build($concrete)
    {
        if (is_callable($concrete)) {
            return $concrete($this);
        }

        if (!class_exists($concrete)) {
            throw new Exception("Class {$concrete} not found");
        }

        $reflector = new ReflectionClass($concrete);
        
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $concrete;
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters());

        return $reflector->newInstanceArgs($dependencies);
    }

    protected function resolveDependencies($dependencies)
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $type = $dependency->getType();
            
            if ($type === null) {
                if ($dependency->isDefaultValueAvailable()) {
                    $results[] = $dependency->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve dependency {$dependency->name}");
                }
            } else {
                $results[] = $this->resolve($type->getName());
            }
        }

        return $results;
    }

    public function make($abstract, $parameters = [])
    {
        return $this->resolve($abstract);
    }
}