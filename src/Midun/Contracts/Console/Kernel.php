<?php

namespace Midun\Contracts\Console;

interface Kernel
{
    /**
     * Handle the console command
     */
    public function handle();
    
    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all();
}
