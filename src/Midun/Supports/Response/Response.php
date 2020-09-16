<?php

namespace Midun\Supports\Response;

use Midun\Http\HttpResponseCode;

class Response
{
    /**
     * Response with json
     * 
     * @param mixed $arguments
     * @param int $code = 200
     * 
     * @return Response
     */
    public final function json($arguments, $code = 200): Response
    {
        (new HttpResponseCode($code));
        header('Content-Type: application/json');

        if ($arguments instanceof \ArrayObject) {
            $arguments = objectToArray($arguments);
        }
        echo json_encode($arguments);

        return $this;
    }
}
