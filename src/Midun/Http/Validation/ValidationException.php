<?php

namespace Midun\Http\Validation;

use Midun\Http\Exceptions\AppException;

class ValidationException extends AppException
{
    /**
     * Errors of validation
     * 
     * @var array
     */
    public $errors = [];
    
    /**
     * AuthenticationException constructor
     * 
     * @param array $errors
     * @param int $code = 422
     */
    public function __construct(array $errors, int $code = 422)
    {
        $this->errors = $errors;

        parent::__construct("Validation error", $code, $errors);
    }

    /**
     * Get list of errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
