<?php

namespace Midun\Contracts\Console;

interface Command
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void;
}
