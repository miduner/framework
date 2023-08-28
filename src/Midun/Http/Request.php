<?php

namespace Midun\Http;

use Auth;
use Midun\Services\File;

class Request
{
    /**
     * Initial constructor of Request
     * Set the properties of request
     */
    public function __construct()
    {
        foreach ($this->getRequest() as $key => $value) {
            $this->$key = $value;
        }
        foreach ($_FILES as $key => $value) {
            $this->$key = new File($value);
        }
    }

    /**
     * Make response array with the request data.
     *
     * @return array
     */
    public function getRequest(): array
    {
        $params = array_merge($_REQUEST, array_map(function ($file) {
            return new File($file);
        }, $_FILES));

        $jsonParams = (array) json_decode(file_get_contents("php://input"));

        $params = array_merge($params, $jsonParams);

        return $params;
    }

    /**
     * Return response array with all request data.
     *
     * @return array $request
     */
    public function all(): array
    {
        return $this->getRequest();
    }

    /**
     * Get all query parameters
     * 
     * @return array
     */
    public function getQueryParams(): array
    {
        return $_GET;
    }

    /**
     * Response only 1 data input from param.
     *
     * @param string $input
     * @return string input value
     * 
     * @return mixed
     */
    public function input(string $input)
    {
        return isset($this->getRequest()[$input]) ? $this->getRequest()[$input] : null;
    }

    /**
     * Response only 1 data input from param.
     * 
     * @param string $input
     * 
     * @return mixed
     */
    public function get(string $input)
    {
        return $this->input($input);
    }

    /**
     * Response only data input from array input.
     *
     * @param array $inputs
     * @return array
     */
    public function only(array $inputs): array
    {
        $request = [];
        
        foreach ($this->getRequest() as $name => $value) {
            if (in_array($name, $inputs)) {
                $request[$name] = $value;
            }
        }
        return $request;
    }

    /**
     * Response data input except array input.
     *
     * @param array $inputs
     * 
     * @return array
     */
    public function except(array $inputs): array
    {
        $request = [];
        foreach ($this->getRequest() as $name => $value) {
            if (!in_array($name, $inputs)) {
                $request[$name] = $value;
            }
        }
        return $request;
    }

    /**
     * Get all headers requested
     * 
     * @return array
     */
    public function headers(): array
    {
        return getallheaders();
    }

    /**
     * Get user from request
     * 
     * @param string $guard
     * 
     * @return object
     */
    public function user(?string $guard = null): ?object
    {
        if (is_null($guard)) {
            $guard = Auth::getCurrentGuard();
        }

        return Auth::guard($guard)->user();
    }

    /**
     * Return request is ajax request
     * 
     * @return bool
     */
    public function isAjax()
    {
        $headers = (array) $this->headers();
        return isset($headers['Accept'])
            && $headers['Accept'] == 'application/json'
            || isset($headers['Content-Type'])
            && $headers['Content-Type'] == 'application/json'
            || isset($headers['x-requested-with'])
            && $headers['x-requested-with'] == 'XMLHttpRequest';
    }

    /**
     * Get request server
     */
    public function server()
    {
        return $_SERVER;
    }

    /**
     * Get method
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->name : null;
    }
}
