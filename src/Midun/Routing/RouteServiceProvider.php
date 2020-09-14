<?php

namespace Midun\Routing;

use Midun\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register singleton routing
     */
    public function register(): void
    {
        $this->app->singleton('route', function () {
            return new \Midun\Routing\Router;
        });
    }
}
