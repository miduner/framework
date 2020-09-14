<?php

namespace Midun\Http\Exceptions;

class UnauthorizedException extends AppException
{
    /**
     * AuthenticationException constructor
     *
     * @param string $message
     * @param int $code = 401
     */
    public function __construct($message = 'Unauthorized !', $code = 401)
    {
        parent::__construct($message, $code);
    }
}
