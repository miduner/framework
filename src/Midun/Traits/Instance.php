<?php

namespace Midun\Traits;

trait Instance
{
    /**
     * Instance of
     */
    private static self $instance;

    /**
     * Initial constructor
     */
    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * Get instance of
     * 
     * @return self
     */
    public static function getInstance(): self
    {
        if(!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }
}
