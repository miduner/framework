<?php

namespace Midun\Contracts\Http;

interface Kernel
{
    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Midun\Http\Request  $request
     * @return mixed
     */
    public function handle(\Midun\Http\Request $request): void;

    /**
     * Get the Laravel application instance.
     *
     * @return \Midun\Container
     */
    public function getApplication(): \Midun\Container;
}
