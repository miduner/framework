<?php

namespace Midun\Http\Exceptions;

use Midun\Application;
use Midun\Container;

class ErrorHandler
{
    private \Midun\Logger\Logger $log;

    public function __construct()
    {
        $this->app = Container::getInstance();

        if ($this->app->make(Application::class)->isLoaded()) {
            $this->app->make('view')->setMaster('');
            ob_get_clean();
            $this->log = $this->app->make('log');
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

        if (!isset($this->log)) {
            die($msg);
        }

        $file = str_replace(base_path(), '', $file);

        $this->log->error($msg);

        $e = new UnknownException($msg);

        $e->render($e);

        exit($errno);
    }
}
