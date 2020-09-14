<?php

namespace Midun\Routing\Controller;

use Midun\ServiceProvider;

class ControllerServiceProvider extends ServiceProvider
{
    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('controller', function () {
            return new \Midun\Routing\Controller\Controller;
        });
    }
}
