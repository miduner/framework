<?php

namespace Midun\Routing;

class Compile
{
    /**
     * Instance of matched routing
     * 
     * @var \Midun\Routing\RouteCollection
     */
    private $route;

    /**
     * List of parameters
     * 
     * @var array
     */
    private $params = [];
    /**
     * Specific character
     * 
     * @var string
     */
    const DEFAULT_SPECIFIC = '@';

    /**
     * Initial constructor
     * 
     * @param \Midun\Routing\RouteCollection $route
     * @param array $params
     */
    public function __construct(RouteCollection $route, array $params)
    {
        $this->route = $route;
        $this->params = $params;
    }

    /**
     * Handle route action
     *
     * @return void
     */
    public function handle()
    {
        $action = $this->route->getAction();
        if (!is_array($action)) {
            $action = explode(Compile::DEFAULT_SPECIFIC, $action);
        }
        switch (count($action)) {
            case 2:
                list($controller, $methodName) = $action;
                break;
            case 1:
                list($controller) = $action;
                $methodName = '__invoke';
                break;
            default:
                throw new RouteException("Controller wrong format !");
                break;
        }
        $namespace = $this->route->getNamespace();

        if (!empty($namespace)) {
            $controller = implode("\\", $namespace) . "\\" . $controller;
        }
        if (class_exists($controller)) {
            $object = app()->build($controller);
            $params = app()->resolveMethodDependencyWithParameters($controller, $methodName, $this->params);
            if (method_exists($controller, $methodName)) {
                return call_user_func_array([$object, $methodName], $params);
            }
            throw new RouteException("Method {$controller}@{$methodName} doesn't exists !");
        }
        throw new RouteException("Class {$controller} doesn't exists !");
    }
}
