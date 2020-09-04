<?php

namespace Midun\Contracts\Auth;

interface Authentication
{
    public function attempt($options = []);
}
