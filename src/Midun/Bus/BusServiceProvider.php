<?php

namespace Midun\Bus;

use Midun\ServiceProvider;

class BusServiceProvider extends ServiceProvider
{
    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(\Midun\Contracts\Bus\Dispatcher::class, \Midun\Bus\Dispatcher::class);
    }
}
