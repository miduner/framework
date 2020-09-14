<?php

namespace Midun\Routing;

class RouteCollection
{
    /**
     * Method of routing
     * 
     * @var string
     */
    private string $methods;

    /**
     * Uri of routing
     * 
     * @var string
     */
    private string $uri;

    /**
     * Action
     * 
     * @var mixed
     */
    private $action;

    /**
     * Name
     * 
     * @var string
     */
    private string $name;

    /**
     * List of middlewares
     * 
     * @var array
     */
    private array $middlewares = [];

    /**
     * Prefix
     * 
     * @var array
     */
    private array $prefix = [];

    /**
     * Namespace
     * 
     * @var array
     */
    private array $namespaces = [];

    /**
     * Initial constructor
     * 
     * @param string $methods
     * @param string $uri
     * @param string $name
     * @param mixed $action
     * @param array $middlewares
     * @param array $prefix
     * @param array $namespaces
     * 
     */
    public function __construct(
        $methods,
        $uri,
        $name,
        $action,
        $middlewares,
        $prefix,
        $namespaces
    ) {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->name = $name;
        $this->action = $action;
        $this->middlewares = $middlewares;
        $this->prefix = is_array($prefix) && !empty($prefix) ? $prefix : [$prefix];
        $this->namespaces = (is_array($namespaces) ? $namespaces : is_string($namespaces)) ? [$namespaces] : null;
    }

    /**
     * Set middleware
     * 
     * @param string|array $middleware
     * 
     * @return self
     */
    public function middleware($middleware): RouteCollection
    {
        if (!is_array($middleware)) {
            array_push($this->middlewares, $middleware);
        } else {
            $this->middlewares = array_merge($this->middlewares, $middleware);
        }
        return $this;
    }

    /**
     * Set namespace
     * 
     * @param string|array $namespace
     * 
     * @return self
     */
    public function namespace($namespace): RouteCollection
    {
        $this->namespaces[] = $namespace;
        return $this;
    }

    /**
     * Set name
     * 
     * @param string $name
     * 
     * @return self
     */
    public function name($name): RouteCollection
    {
        $this->name .= $name;
        return $this;
    }

    /**
     * Set prefix
     * 
     * @param string $prefix
     * 
     * @return self
     */
    public function prefix($prefix): RouteCollection
    {
        $this->prefix[] = $prefix;
        return $this;
    }

    /**
     * Get uri
     * 
     * @return string
     */
    public function getUri(): string
    {
        return empty($this->uri)
            || !empty($this->uri)
            && $this->uri[0]
            != Routing::ROUTING_SEPARATOR
            ? Routing::ROUTING_SEPARATOR . $this->uri
            : $this->uri;
    }

    /**
     * Get method
     * 
     * @return string
     */
    public function getMethods(): string
    {
        return $this->methods;
    }

    /**
     * Get name 
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get action
     * 
     * @return string|array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get middleware
     * 
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Get prefix
     * 
     * @return array
     */
    public function getPrefix(): array
    {
        return !empty($this->prefix && !empty($this->prefix[0])) ? $this->prefix : [];
    }

    /**
     * Get namespace
     * 
     * @return array
     */
    public function getNamespace(): array
    {
        return $this->namespaces;
    }
}
