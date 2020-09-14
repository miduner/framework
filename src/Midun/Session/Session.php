<?php

namespace Midun\Session;

session_start();
class Session
{
    /**
     * Check exists session key
     * 
     * @param string $key
     * 
     * @return bool
     */
    public function isset(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Set a session
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Unset a session
     * 
     * @param string $key
     * 
     * @return void
     */
    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get session by key
     * 
     * @param string $key
     * 
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->isset($key) ? $_SESSION[$key] : null;
    }

    /**
     * Get all session storage
     * 
     * @return array
     */
    public function storage(): array
    {
        return $_SESSION;
    }
}
