<?php

namespace Midun\Http\Exceptions;

class UnknownException extends AppException
{
    /**
     * AuthenticationException constructor
     *
     * @param string $message
     * @param int $code = 400
     */
    public function __construct($message = 'Unknown !', $code = 400)
    {
        parent::__construct($message, $code);
    }
}
