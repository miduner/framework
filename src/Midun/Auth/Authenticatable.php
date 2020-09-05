<?php

namespace Midun\Auth;

use DB;
use Hash;
use Session;
use Firebase\JWT\JWT;
use Midun\Eloquent\Model;
use Midun\Contracts\Auth\Authentication;

class Authenticatable implements Authentication
{
    /**
     * Guard of user
     * 
     * @var string
     */
    private $guard;

    /**
     * Provider of guard
     * 
     * @var string
     */
    private $provider;

    /**
     * Model of user
     * 
     * @var string
     */
    private $model;

    /**
     * Model object bound
     * 
     * @var object
     */
    private $object;

    /**
     * Check condition and set user
     *
     * @param array $options
     *
     * @return boolean
     */
    public function attempt($options = [])
    {
        $model = new $this->model;
        $columnPassword = $model->password();
        $table = $model->table();
        $paramPassword = $options[$columnPassword];
        unset($options[$columnPassword]);

        $object = DB::table($table)->where($options)->first();

        if (!$object || $object && !Hash::check($paramPassword, $object->password)) {
            return false;
        }

        return $this->setUserAuth(
            $this->model::where($options)->firstOrFail()
        );
    }

    /**
     * Get user available
     * 
     * @throws \Midun\Http\Exceptions\AppException
     *
     * @return object/null
     */
    public function user()
    {
        $guardDriver = config("auth.guards.{$this->guard}.driver");

        switch ($guardDriver) {
            case 'session':
                return Session::get('user');
            case 'jwt':
                if (!is_null($this->object)) {
                    return $this->object;
                }

                $key = config('jwt.secret');

                $hash = config('jwt.hash');

                if (empty($key)) {
                    throw new AuthenticationException("Please install the JWT authentication");
                }

                if (empty($hash)) {
                    throw new AuthenticationException("Please set hash type in config/jwt.php");
                }

                $header = getallheaders();

                if (!isset($header['Authorization'])) {
                    return null;
                }

                $bearerToken = str_replace("Bearer ", '', $header['Authorization']);

                try {
                    $jwt = app()->make(JWT::class);

                    $decode = $jwt->decode($bearerToken, $this->trueFormatKey($key), [$hash]);

                    $primaryKey = app()->make($this->model)->primaryKey();

                    $modelObject = $this->model::findOrFail($decode->object->{$primaryKey});

                    return $modelObject;
                } catch (\Firebase\JWT\ExpiredException $e) {
                    throw new AuthenticationException($e->getMessage());
                }
        }
    }

    /**
     * Make true format for jwt key
     * 
     * @param string $key
     * 
     * @return string
     */
    public function trueFormatKey(string $key)
    {
        return base64_decode(strtr($key, '-_', '+/'));
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout()
    {
        $guardDriver = config("auth.guards.{$this->guard}.driver");
        switch ($guardDriver) {
            case 'session':
                return Session::unset('user');
                break;
            case 'jwt':
                return true;
                break;
        }
    }

    /**
     * Checking has user login
     *
     * @return boolean
     */
    public function check()
    {
        if (!is_null($this->user()) && !empty($this->user())) {
            return true;
        }
        return false;
    }

    /**
     * Set user to application
     *
     * @param Model $user
     *
     * @return true
     */
    private function setUserAuth(Model $user)
    {
        $this->object = $user;

        $guardDriver = config("auth.guards.{$this->guard}.driver");

        switch ($guardDriver) {
            case 'session':
                Session::set('user', $this->object);
                break;
        }
        return true;
    }

    /**
     * Set guard for authentication
     *
     * @param string $name
     *
     * @return $this
     */
    public function guard($name = null)
    {
        $this->guard = $name ?: $this->getDefaultGuard();
        $this->provider = config("auth.guards.{$this->guard}.provider");
        $this->model = config("auth.providers.{$this->provider}.model");
        return $this;
    }

    /**
     * Get default guard config
     * 
     * @return string
     */
    private function getDefaultGuard()
    {
        return config('auth.defaults.guard');
    }

    /**
     * Get current guard config
     * 
     * @return string
     */
    public function getCurrentGuard()
    {
        return $this->guard;
    }
}
