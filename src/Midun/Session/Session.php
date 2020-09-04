<?php

namespace Midun\Session;

session_start();
class Session
{
    public function isset($key)
    {
        return isset($_SESSION[$key]);
    }

    public function set($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    public function unset($key)
    {
        unset($_SESSION[$key]);
    }

    public function get($key)
    {
        return $this->isset($key) ? $_SESSION[$key] : null;
    }

    public function storage()
    {
        return $_SESSION;
    }
}
