<?php

namespace Midun\Database\QueryBuilder;

use Midun\ServiceProvider;

class QueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('db', function () {
            return new \Midun\Database\QueryBuilder\QueryBuilder;
        });
    }
}
