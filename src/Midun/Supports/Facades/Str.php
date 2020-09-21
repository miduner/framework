<?php

namespace Midun\Supports\Facades;

use Midun\Supports\Facade;
use Midun\Supports\Str as SupportsStr;

class Str extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'str';
    }

    /**
     * Call static function
     * 
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return (new SupportsStr)->$method(...$arguments);
    }

    /**
     * Call function
     * 
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return (new SupportsStr)->$method(...$arguments);
    }
}
