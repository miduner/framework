<?php

namespace Midun\Database\Connections;

use Midun\Http\Exceptions\RuntimeException;
use Midun\ServiceProvider;

class ConnectionServiceProvider extends ServiceProvider
{
    /**
     * Register connection service provider
     * 
     * @return void
     * 
     * @throws RuntimeException
     */
    public function register(): void
    {
        $this->app->singleton('connection', function () {
            try {
                $default = config('database.default');
                switch (true) {
                    case $default === 'mysql':
                        return new \Midun\Database\Connections\Mysql\Connection;
                    case $default === 'pgsql':
                        return new \Midun\Database\Connections\PostgreSql\Connection;
                    default:
                        throw new RuntimeException("Driver {$default} not found.");
                }
            } catch (RuntimeException $e) {
                throw new RuntimeException($e->getMessage());
            }
        });
    }
}
