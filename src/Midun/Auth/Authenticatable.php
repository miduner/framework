<?php

namespace Midun\Auth;

use DB;
use Hash;
use Session;
use Firebase\JWT\JWT;
use Midun\Eloquent\Model;
use Midun\Http\Exceptions\AppException;
use Midun\Contracts\Auth\Authentication;

/**
 * Class Authenticatable
 *
 * @package Midun\Auth
 */
class Authenticatable implements Authentication
{
    /**
     * Guard of user
     * 
     * @var string
     */
    private string $guard = "";

    /**
     * Provider of guard
     * 
     * @var string
     */
    private string $provider = "";

    /**
     * Model of user
     * 
     * @var string
     */
    private string $model = "";

    /**
     * Model object bound
     * 
     * @var Model
     */
    private ?Model $object = null;

    /**
     * Check condition and set user
     *
     * @param array $options
     *
     * @return boolean
	 *
	 * @throws AuthenticationException
     */
    public function attempt(array $options = []): bool
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
     * @throws AppException
     *
     * @return Model/null
     */
    public function user(): ?Model
    {
        if (!is_null($this->getObject())) {
            return $this->getObject();
        }

        $guardDriver = $this->getConfigDriverFromGuard(
            $this->getCurrentGuard()
        );

        switch ($guardDriver) {
            case 'session':
                return Session::get('user');
            case 'jwt':
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
                } catch (\Firebase\JWT\SignatureInvalidException $e) {
                    throw new AuthenticationException($e->getMessage());
                }
                break;
            default:
                throw new AuthenticationException('Unknown authentication');
        }
    }

    /**
     * Make true format for jwt key
     * 
     * @param string $key
     * 
     * @return string
     */
    public function trueFormatKey(string $key): string
    {
        return base64_decode(strtr($key, '-_', '+/'));
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        $guardDriver = $this->getConfigDriverFromGuard(
            $this->getCurrentGuard()
        );

        switch ($guardDriver) {
            case 'session':
                Session::unset('user');
                break;
            case 'jwt':
                break;
        }
    }

    /**
     * Checking has user login
     *
     * @return boolean
	 *
	 * @throws AppException
     */
    public function check(): bool
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
     * @return bool
	 *
	 * @throws AuthenticationException
     */
    private function setUserAuth(Model $user): bool
    {
        $this->setObject($user);

        $guardDriver = $this->getConfigDriverFromGuard(
            $this->getCurrentGuard()
        );

        switch ($guardDriver) {
            case 'session':
                Session::set('user', $this->getObject());
                break;
            case 'jwt':
                break;
            default:
                throw new AuthenticationException("Unknown authentication");
        }

        return true;
    }

    /**
     * Set guard for authentication
     *
     * @param string $guard
     *
     * @return $this
     */
    public function guard($guard = ""): Authenticatable
    {
        if (empty($guard)) {
            $guard = $this->getDefaultGuard();
        }

        $this->setGuard($guard);

        $guard = $this->getCurrentGuard();

        $this->setProvider(
            $this->getConfigProviderFromGuard($guard)
        );

        $provider = $this->getProvider();

        $this->setModel(
            $this->getConfigModelFromProvider($provider)
        );

        return $this;
    }

    /**
     * Get configuration model from provider
     * 
     * @param string $provider
     * 
     * @return string
     */
    protected function getConfigModelFromProvider(string $provider): string
    {
        return config("auth.providers.{$provider}.model");
    }

    /**
     * Get configuration provider from guard
     * 
     * @param string $guard
     * 
     * @return string
     */
    protected function getConfigProviderFromGuard(string $guard): string
    {
        return config("auth.guards.{$guard}.provider");
    }

    /**
     * Get configuration provider from guard
     * 
     * @param string $guard
     * 
     * @return string
     */
    protected function getConfigDriverFromGuard(string $guard): string
    {
        return config("auth.guards.{$guard}.driver");
    }

    /**
     * Set model name
     * 
     * @param string $model
     * 
     * @return void
     */
    protected function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * Get current provider
     * 
     * @return string
     */
    protected function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Set provider
     * 
     * @param string $provider
     * 
     * @return void
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * Set guard
     * 
     * @param string $guard
     * 
     * @return void
     */
    public function setGuard(string $guard): void
    {
        $this->guard = $guard;
    }

    /**
     * Get default guard config
     * 
     * @return string
     */
    private function getDefaultGuard(): string
    {
        return config('auth.defaults.guard');
    }

    /**
     * Get current guard config
     * 
     * @return string
     */
    public function getCurrentGuard(): string
    {
        return $this->guard;
    }

    /**
     * Set object
     * 
     * @param object $object
     * 
     * @return void
     */
    protected function setObject(Model $object): void
    {
        $this->object = $object;
    }

    /**
     * Get current object bound
     * 
     * @return null|Model
     */
    protected function getObject(): ?Model
    {
        return $this->object;
    }
}
