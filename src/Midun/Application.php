<?php

namespace Midun;

use Midun\Container;
use Midun\Traits\Instance;
use Midun\Configuration\Config;
use Midun\Http\Exceptions\ErrorHandler;
use Midun\Http\Exceptions\RuntimeException;

class Application
{
    use Instance;

    /**
     * Container instance
     * 
     * @var Container
     */
    private Container $container;

    /**
     * Instance of configuration
     * 
     * @var Config
     */
    private Config $config;


    /**
     * Flag check providers is loaded
     * 
     * @var bool
     */
    private bool $loaded = false;

    /**
     * Initial constructor
     * 
     * @param Container $container
     * 
     * Set configuration instance
     * 
     * @return mixed
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->registerConfigProvider();

        new AliasLoader();

        register_shutdown_function([$this, 'whenShutDown']);

        $this->setErrorHandler();
    }

    /**
     * Register service providers
     * 
     * @return void
     */
    public function registerServiceProvider(): void
    {
        $providers = $this->container->make('config')->getConfig('app.providers');

        if (!empty($providers)) {
            foreach ($providers as $provider) {
                $provider = new $provider;
                $provider->register();
            }
            foreach ($providers as $provider) {
                $provider = new $provider;
                $provider->boot();
            }
        }
    }

    /**
     * Register initial configuration provider
     * 
     * @return void
     */
    private function registerConfigProvider(): void
    {
        $this->container->singleton('config', function () {
            return new \Midun\Configuration\Config();
        });
    }

    /**
     * Get status load provider
     * 
     * @return bool
     */
    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * Set state load provider
     * 
     * @param bool $isLoad
     * 
     * @return void
     */
    private function setLoadState(bool $isLoad): void
    {
        $this->loaded = $isLoad;
    }

    /**
     * Load configuration
     * 
     * @return void
     */
    private function loadConfiguration(): void
    {
        $cache = array_filter(scandir(cache_path()), function ($item) {
            return strpos($item, '.php') !== false;
        });
        foreach ($cache as $item) {
            $key = str_replace('.php', '', $item);

            $value = require cache_path($item);

            $this->container->make('config')->setConfig($key, $value);
        }
    }

    /**
     * Run the application
     * 
     * @return void
     */
    public function run(): void
    {
        $this->loadConfiguration();
        $this->registerServiceProvider();
        $this->setLoadState(true);
    }

    /**
     * Terminate the application
     */
    public function terminate(): void
    { }

    /**
     * Set error handler
     * 
     * @return void
     * 
     * @throws RuntimeException
     */
    public function whenShutDown(): void
    {
        $last_error = error_get_last();
        if (!is_null($last_error)) {
            ob_clean();
            $handler = new ErrorHandler;

            $handler->errorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }

    /**
     * Set error handler
     * 
     * @return mixed
     */
    public function setErrorHandler()
    {
        set_error_handler(function () {
            $handler = new ErrorHandler;

            return $handler->errorHandler(...func_get_args());
        });
    }
}
