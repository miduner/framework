<?php

namespace Midun\Routing;

use Midun\Traits\Routing\Resource;

class RouteResource
{
    use Resource;

    /**
     * List of middleware
     * 
     * @var array
     */
    private array $middlewares = [];

    /**
     * Prefix of routes
     * 
     * @var mixed
     */
    private array $prefix = [];

    /**
     * Namespace of route
     * 
     * @var array
     */
    private array $namespaces = [];

    /**
     * Resources of route
     * 
     * @var array
     */
    private array $resources = [];

    /**
     * Except for route
     * 
     * @var array
     */
    private array $except = [];

    /**
     * Initial RouteResource
     * 
     * @param array $resource
     * @param string $name
     * @param array $middlewares
     * @param array $prefix
     * @param array $namespaces
     */
    public function __construct()
    {
        list(
            $resource,
            $name,
            $middlewares,
            $prefix,
            $namespaces
        ) = func_get_args();

        $this->middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        $this->prefix = is_array($prefix) ? $prefix : [$prefix];
        $this->namespaces = is_array($namespaces) ? $namespaces : [$namespaces];
        $this->name = $name;
        $this->resources = is_array($resource) ? $resource : [$resource];
    }
    /**
     * Except function
     * @param array $methods
     *
     * @return self
     */
    public function except(array $methods): RouteResource
    {
        $this->except = $methods;
        return $this;
    }

    /**
     * Set middleware
     * 
     * @param mixed $middleware
     * 
     * @return self
     */
    public function middleware($middleware): RouteResource
    {
        if (!is_array($middleware)) {
            array_push($this->middlewares, $middleware);
        } else {
            $this->middlewares = array_merge($this->middlewares, $middleware);
        }
        return $this;
    }

    /**
     * Register namespace
     * 
     * @param string $namespace
     * 
     * @return self
     */
    public function namespace(string $namespace): RouteResource
    {
        $this->namespaces[] = $namespace;
        return $this;
    }

    /**
     * Register name of route
     * 
     * @param string $name
     * 
     * @return self
     */
    public function name(string $name): RouteResource
    {
        $this->name .= $name;
        return $this;
    }

    /**
     * Register prefix
     * 
     * @param string $prefix
     * 
     * @return self
     */
    public function prefix(string $prefix): RouteResource
    {
        $this->prefix[] = $prefix;
        return $this;
    }

    /**
     * Parse list resource to RouteCollections
     * 
     * @return array
     */
    public function parse(): array
    {
        $routes = [];

        foreach ($this->resources as $resource) {
            $routes[] = $this->makeIndex($resource);
            $routes[] = $this->makeCreate($resource);
            $routes[] = $this->makeShow($resource);
            $routes[] = $this->makeStore($resource);
            $routes[] = $this->makeEdit($resource);
            $routes[] = $this->makeUpdate($resource);
            $routes[] = $this->makeDelete($resource);
        }

        return array_filter($routes, fn($route) => !is_null($route));
    }
}
