<?php

namespace Midun\Supports;

abstract class Facade
{
    /**
     * Get facade of entity
     * 
     * @return string
     */
    protected static abstract function getFacadeAccessor();

    /**
     * Call static handler
     * 
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed|object
     */
    public static function __callStatic($method, $arguments)
    {
        return app()->make(static::getFacadeAccessor())->$method(...$arguments);
    }

    /**
     * Call handler
     * 
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed|object
     */
    public function __call($method, $arguments)
    {
        return app()->make(static::getFacadeAccessor())->$method(...$arguments);
    }
}
