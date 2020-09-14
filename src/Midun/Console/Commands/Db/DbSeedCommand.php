<?php

namespace Midun\Console\Commands\Db;

use Midun\Console\Command;

class DbSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'db:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Run seeding data database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handle the command
     * 
     * @return void
     */
    public function handle(): void
    {
        $this->output->printSuccess("Seeded successfully.");
    }
}
