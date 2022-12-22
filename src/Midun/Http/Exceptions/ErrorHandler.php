<?php

namespace Midun\Http\Exceptions;

use Midun\Application;
use Midun\Container;

class ErrorHandler
{
    public function __construct()
    {
        $this->app = Container::getInstance();

        if ($this->app->make(Application::class)->isLoaded()) {
            $this->app->make('view')->setMaster('');
            ob_get_clean();
        }
    }

    /**
     * Error handler
     * 
     * @param int $errno
     * @param string $errstr
     * @param string $file
     * @param int $line
     * 
     * @return void
     */
    public function errorHandler(int $errno, string $errstr, string $file, int $line): void
    {
        $msg = "{$errstr} on line {$line} in file {$file}";

        $file = str_replace(base_path(), '', $file);

        $e = new UnknownException($msg);

        $e->render($e);

        exit($errno);
    }
}
