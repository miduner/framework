<?php

namespace Midun\Contracts\Console;

interface Kernel
{
    /**
     * Handle the console command
     */
    public function handle(): void;

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Call a single command
     * 
     * @param string $command
     * 
     * @return void
     */
    public function call(string $command, array $options = []): void;
}
