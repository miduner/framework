<?php

namespace Midun\Supports\Response;

use Midun\ServiceProvider;

class DataResponseServiceProvider extends ServiceProvider
{
    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('response', function () {
            return new \Midun\Supports\Response\DataResponse;
        });
    }
}
