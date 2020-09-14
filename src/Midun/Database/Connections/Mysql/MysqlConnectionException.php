<?php

namespace Midun\Database\Connections\Mysql;

use Midun\Http\Exceptions\AppException;

class MysqlConnectionException extends AppException
{
    /**
     * AuthenticationException constructor
     * 
     * @param string $message
     * @param int $code = 400
     */
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
