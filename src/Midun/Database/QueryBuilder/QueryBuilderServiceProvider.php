<?php

namespace Midun\Database\QueryBuilder;

use Midun\ServiceProvider;

class QueryBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton('db', function () {
            return new \Midun\Database\QueryBuilder\QueryBuilder;
        });
    }
}
