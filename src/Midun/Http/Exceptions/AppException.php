<?php

namespace Midun\Http\Exceptions;

use Exception;
use Midun\Application;
use Midun\Http\Validation\ValidationException;

class AppException extends Exception
{
    /**
     * AppException constructor
     * 
     * @return void
     */
    public function __construct(string $message, int $code = 400)
    {
        $this->writeLog($message);

        parent::__construct($message, $code);

        if (PHP_SAPI === 'cli') {
            die($message);
        }

        set_exception_handler([$this, 'render']);

        $this->report();
    }

    /**
     * Render exception 
     * 
     * @param \Exception $exception
     * 
     * @return mixed
     */
    public function render(\Exception $exception)
    {
        $errors = [];

        if ($exception instanceof ValidationException) {
            $errors = $this->getErrors();
        }

        if (request()->isAjax()) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
                'errors' => $errors
            ], $this->code);
        }

        return app('view')->render('exception', compact('exception'));
    }

    /**
     * Write exception message to log
     * 
     * @param string $message
     * 
     * @return void
     */
    private function writeLog(string $message): void
    {
        if (app()->make(Application::class)->isLoaded()) {

            app()->make('log')->error(
                (new \ReflectionClass(
                    static::class
                ))->getShortName() . " throws $message from " . $this->getFile() . " line " . $this->getLine()
            );
        }
    }

    /**
     * Report exception
     * 
     * @return void
     */
    protected function report(): void
    {
        // echo 'Reported !';
    }
}
