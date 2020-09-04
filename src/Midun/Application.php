<?php

namespace Midun;

use Midun\Traits\Instance;

class Application
{
    use Instance;
    /**
     * Instance of configuration
     * 
     * @var \Midun\Configuration\Config
     */
    private $config;


    /**
     * Flag check providers is loaded
     * 
     * @var bool
     */
    private $loaded = false;

    /**
     * Initial constructor
     * 
     * Set configuration instance
     */
    public function __construct()
    {
        $this->config = \Midun\Container::getInstance()->make(\Midun\Configuration\Config::class);

        new AliasLoader();
    }

    /**
     * Register service providers
     * 
     * @return void
     */
    public function registerServiceProvider()
    {
        $providers = $this->config->getConfig('app.providers');

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
     * Get status load provider
     * 
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Set state load provider
     * 
     * @param bool $isLoad
     */
    private function setLoadState(bool $isLoad)
    {
        $this->loaded = $isLoad;
    }

    /**
     * Load configuration
     * 
     * @return void
     */
    private function loadConfiguration()
    {
        $cache = array_filter(scandir(cache_path()), function ($item) {
            return strpos($item, '.php') !== false;
        });
        foreach ($cache as $item) {
            $key = str_replace('.php', '', $item);

            $value = require cache_path($item);

            $this->config->setConfig($key, $value);
        }
    }

    /**
     * Run the application
     * 
     * @return void
     */
    public function run()
    {
        $this->loadConfiguration();
        $this->registerServiceProvider();
        $this->setLoadState(true);
    }

    /**
     * Terminate the application
     */
    public function terminate()
    { }
}
