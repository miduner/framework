<?php

namespace Midun\Supports\Facades;

use Midun\Supports\Facade;

class Logger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'log';
    }
}
