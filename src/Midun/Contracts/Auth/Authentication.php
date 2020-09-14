<?php

namespace Midun\Contracts\Auth;

interface Authentication
{
    /**
     * Attempt an options
     * 
     * @param array $options
     * 
     * @return boolean
     */
    public function attempt(array $options = []): bool;

    /**
     * Get user available
     * 
     * @throws \Midun\Http\Exceptions\AppException
     *
     * @return \Midun\Eloquent\Model|null
     */
    public function user(): ?\Midun\Eloquent\Model;

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void;

    /**
     * Checking has user login
     *
     * @return boolean
     */
    public function check(): bool;

    /**
     * Set guard for authentication
     *
     * @param string $guard
     *
     * @return self
     */
    public function guard($guard = ""): Authentication;
}
