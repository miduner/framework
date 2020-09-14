<?php

namespace Midun\Routing;

use Midun\Routing\RouteCollection;

class Router
{
    /**
     * List of middleware
     * 
     * @var array
     */
    private array $middlewares = [];

    /**
     * Prefix of routes
     * 
     * @var string
     */
    private string $prefix = "";

    /**
     * Name of route
     * 
     * @var string
     */
    private string $name = "";

    /**
     * Namespace of route
     * 
     * @var string
     */
    private string $namespace;

    /**
     * Except method resource
     * 
     * @var array
     */
    private array $except;

    /**
     * List of resources
     * 
     * @var array
     */
    private array $resources = [];

    /**
     * List of routes
     * 
     * @var array
     */
    private array $routes = [];

    /**
     * Get method
     * 
     * @param string $uri
     * @param mixed $action
     * 
     * @return RouteCollection
     */
    public function get(string $uri, $action): RouteCollection
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Post method
     * 
     * @param string $uri
     * @param mixed $action
     * 
     * @return RouteCollection
     */
    public function post(string $uri, $action): RouteCollection
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Put method
     * 
     * @param string $uri
     * @param mixed $action
     * 
     * @return RouteCollection
     */
    public function put(string $uri, $action): RouteCollection
    {
        return $this->addRoute('PUT', $uri, $action);
    }
    /**
     * Patch method
     * 
     * @param string $uri
     * @param mixed $action
     * 
     * @return RouteCollection
     */
    public function patch($uri, $action): RouteCollection
    {
        return $this->addRoute('PATCH', $uri, $action);
    }
    /**
     * Any method
     * 
     * @param string $uri
     * @param mixed $action
     * 
     * @return RouteCollection
     */
    public function any(string $uri, $action): RouteCollection
    {
        return $this->addRoute('GET|POST', $uri, $action);
    }
    /**
     * Delete method
     * 
     * @param string $uri
     * @param mixed $action
     * 
     * @return RouteCollection
     */
    public function delete(string $uri, $action): RouteCollection
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add routing
     * 
     * @param string $methods
     * @param string $uri
     * @param mixed $action
     * 
     * @return RouteCollection
     */
    private function addRoute(string $methods, string $uri, $action): RouteCollection
    {
        $middlewares = $this->middlewares;
        $prefix = $this->prefix;
        $namespace = $this->namespace;
        $name = $this->name;

        $router = new RouteCollection($methods, $uri, $name, $action, $middlewares, $prefix, $namespace);
        $this->routes[] = $router;
        return $router;
    }

    /**
     * Add middleware
     * 
     * @param mixed $middleware
     * 
     * @return self
     */
    public function middleware($middleware): Router
    {
        if (!is_array($middleware)) {
            array_push($this->middlewares, $middleware);
        } else {
            $this->middlewares = array_merge($this->middlewares, $middleware);
        }
        return $this;
    }

    /**
     * Register route
     * 
     * @return true
     */
    public function register(): bool
    {
        $this->middlewares = [];
        $this->prefix = "";
        $this->namespace = "";
        $this->name = "";
        $this->except = [];
        $this->resources = [];
        return true;
    }

    /**
     * Add prefix
     * 
     * @param string $prefix
     * 
     * @return self
     */
    public function prefix(string $prefix): Router
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Add namespace
     * 
     * @param string $namespace
     * 
     * @return self
     */
    public function namespace(string $namespace): Router
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Include route file with parameters
     * 
     * @param string $path
     * 
     * @return RouteCollection
     * 
     * @throws AppException
     */
    public function group(string $path): Router
    {
        if (file_exists($path)) {
            require $path;
            return $this;
        }
        throw new AppException("$path not found");
    }

    /**
     * Register resource
     *
     * @param string $uri
     * @param string $action
     *
     * @return RouteResource
     */
    public function resource(string $uri, $action): RouteResource
    {
        $resource = [
            [
                compact('uri', 'action')
            ],
            $this->name, $this->middlewares, $this->prefix, $this->namespace
        ];
        $routeResource = new RouteResource(...$resource);
        $this->routes[] = $routeResource;
        return $routeResource;
    }

    /**
     * Register many routes
     *
     * @param array $resources
     *
     * @return RouteResource
     */
    public function resources(array $resources): RouteResource
    {
        $middlewares = $this->middlewares;
        $prefix = $this->prefix;
        $namespace = $this->namespace;
        $name = $this->name;
        foreach ($resources as $key => $resource) {
            $items[] = [
                'uri' => $key,
                'action' => $resource
            ];
        }
        $resources = [$items, $name, $middlewares, $prefix, $namespace];
        $routeResource = new RouteResource(...$resources);
        $this->routes[] = $routeResource;
        return $routeResource;
    }

    /**
     * Get list routes
     * 
     * @return array
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * Run the routing
     * 
     * @return mixed
     */
    public function run()
    {
        $routing = new Routing($this->collect());

        return $routing->find();
    }

    /**
     * Collect all routing defined
     * 
     * @return array
     */
    public function collect(): array
    {
        $routes = [];

        foreach ($this->routes() as $object) {
            if ($object instanceof RouteResource) {
                $routes = array_merge($routes, $object->parse());
            } else {
                $routes[] = $object;
            }
        }

        return $routes;
    }

    /**
     * Callable action to controller method
     * 
     * @param array $action
     * @param array $params = []
     * 
     * @return mixed
     */
    public function callableAction(array $action, array $params = [])
    {
        $rc = new RouteCollection(
            __FUNCTION__,
            __FUNCTION__,
            __FUNCTION__,
            $action,
            __FUNCTION__,
            __FUNCTION__,
            NULL
        );

        $compile = new Compile($rc, $params);

        return $compile->handle();
    }
}
