<?php

namespace Midun\Http\Validation;

use Midun\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Run after the application already registered service,
     * if you want to use 3rd or outside service,
     * please implement them to the boot method.
     * 
     * @return void
     */
    public function boot(): void
    {
        $validator = $this->app->make('validator');
        $validator->setRules([
            'required',
            'min',
            'max',
            'number',
            'string',
            'file',
            'image',
            'video',
            'audio',
            'email',
            'unique'
        ]);
    }

    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('validator', function () {
            return new Validator();
        });
    }
}
