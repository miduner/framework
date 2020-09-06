<?php

namespace Midun\Routing;

use Midun\Http\Exceptions\RuntimeException;

class Compile
{
    /**
     * Method execute
     * 
     * @var string
     */
    private $method;

    /**
     * Controller execute
     * 
     * @var string
     */
    private $controller;

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
        $this->makeRoute($route);
        $this->makeParams($params);

        $this->findingTarget(
            $this->getAction()
        );

        $this->app = \Midun\Container::getInstance();
    }

    /**
     * Handle route action
     *
     * @return void
     * 
     * @throws RouteException
     * @throws RuntimeException
     */
    public function handle()
    {
        $controller = $this->getFullNameSpace(
            $this->getController()
        );
        $method = $this->getMethod();

        if (!class_exists($controller) || !method_exists($controller, $method)) {
            throw new RouteException("Endpoint target `{$controller}@{$method}` doesn't exists");
        }
        try {
            $object = $this->app->build($controller);
            $params = $this->app->resolveMethodDependencyWithParameters(
                $controller,
                $method,
                $this->getParams()
            );

            return call_user_func_array([$object, $method], $params);
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage());
        } catch (\Error $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Find the target controller and method
     * 
     * @param string|array $action
     * 
     * @return void
     */
    private function findingTarget($action)
    {
        if (!is_array($action)) {
            $action = explode(Compile::DEFAULT_SPECIFIC, $action);
        }
        switch (count($action)) {
            case 2:
                list($controller, $method) = $action;
                break;
            case 1:
                list($controller) = $action;
                $method = '__invoke';
                break;
            default:
                throw new RouteException("Controller wrong format !");
                break;
        }

        $this->setMethod($method);
        $this->setController($controller);
    }

    /**
     * Set method name
     * 
     * @param string $method
     * 
     * @return void
     */
    private function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * Get method name
     * 
     * @return string
     */
    private function getMethod()
    {
        return $this->method;
    }

    /**
     * Set controller name
     * 
     * @param string $controller
     * 
     * @return void
     */
    private function setController(string $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller name
     * 
     * @return string
     */
    private function getController()
    {
        return $this->controller;
    }

    /**
     * Make route
     * 
     * @param RouteCollection $route
     * 
     * @return void
     */
    private function makeRoute(RouteCollection $route)
    {
        $this->route = $route;
    }

    /**
     * Get params value
     * 
     * @return array|null
     */
    private function getParams()
    {
        return $this->params;
    }

    /**
     * Get route instance
     * 
     * @return RouteCollection
     */
    private function getRoute()
    {
        return $this->route;
    }

    /**
     * Get action value
     * 
     * @return array|string
     */
    private function getAction()
    {
        return $this->getRoute()->{__FUNCTION__}();
    }

    /**
     * Get namespace value
     * 
     * @param string $controller
     * 
     * @return string
     */
    private function getFullNamespace(string $controller)
    {
        $namespace = $this->getRoute()->getNamespace();

        return !empty($namespace)
            ? implode("\\", $namespace) . "\\" . $controller
            : $controller;
    }

    /**
     * Make params
     * 
     * @param array $params
     * 
     * @return void
     */
    private function makeParams(array $params)
    {
        $this->params = $params;
    }
}
