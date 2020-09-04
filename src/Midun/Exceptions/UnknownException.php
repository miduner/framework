<?php

namespace Midun\Exceptions;

use Midun\Http\Exceptions\AppException;

class UnknownException extends AppException
{
    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
