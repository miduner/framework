<?php

namespace Midun\Logger;

use Midun\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    /**
     * Booting
     */
    public function boot(): void
    {
        $logger = $this->app->make('log');

        $logger->setDirectory(config('logger.directory'));

        $logger->setWriteLogByDate(config('logger.by_date'));
    }

    /**
     * Register singleton routing
     */
    public function register(): void
    {
        $this->app->singleton('log', function () {
            return new \Midun\Logger\Logger();
        });
    }
}
