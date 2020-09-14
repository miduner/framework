<?php

namespace Midun\Http;

use Midun\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('request', function () {
            return new \Midun\Http\Request;
        });
    }
}
