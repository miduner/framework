<?php

namespace Midun;

use Midun\Container;

abstract class ServiceProvider
{
    /**
     * Instance of Container
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Initial constructor
     *
     * @var \Midun\Container $app
     */
    public function __construct()
    {
        $this->app = Container::getInstance();
    }

    /**
     * Run after the application already registered service,
     * if you want to use 3rd or outside service,
     * please implement them to the boot method.
     * 
     * @return void
     */
    public function boot(): void
    {

    }

    /**
     * Register all of the service providers that you
     * import in config/app.php -> providers
     * 
     * @return void
     */
    abstract public function register(): void;
}
