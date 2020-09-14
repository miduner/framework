<?php

namespace Midun\Http\Validation;

use Midun\Http\Exceptions\AppException;

class ValidationException extends AppException
{
    /**
     * AuthenticationException constructor
     * 
     * @param string $message
     * @param int $code = 400
     */
    public function __construct(string $message, int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
