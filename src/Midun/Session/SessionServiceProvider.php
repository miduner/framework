<?php

namespace Midun\Session;

use Midun\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('session', function () {
            return new \Midun\Session\Session;
        });
    }
}
