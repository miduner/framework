<?php

namespace Midun\Configuration;

use Midun\ServiceProvider;

class ConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Register 3rd-party services
     */
    public function boot(): void
    {
        date_default_timezone_set(config('app.timezone'));
    }

    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('config', function () {
            return $this->app->make('config');
        });
    }
}
