<?php

namespace Midun\Storage;

use Midun\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $storage = $this->app->make('storage');

        $storage->disk(config('storage.default'));
    }

    public function register(): void
    {
        $this->app->singleton('storage', function () {
            return new \Midun\Storage\Storage;
        });
    }
}
