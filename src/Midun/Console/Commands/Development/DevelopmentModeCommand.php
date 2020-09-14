<?php

namespace Midun\Console\Commands\Development;

use Midun\Console\Command;

class DevelopmentModeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'development:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Enable development mode';

    /**
     * Others signature
     * 
     * @var array
     */
    protected array $otherSignatures = [
        "dev:mode"
    ];

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
        if (file_exists(base_path('Midun'))) {
        	$this->output->printError('The `{miduner}/Midun` directory already exists.');
        	exit(1);
        }

        $this->app->make('fileSystem')->link(
            base_path('vendor/miduner/miduner/src/Midun'),
            base_path('Midun')
        );

        $this->output->printSuccess('The [{miduner}/Midun] directory has been linked.');
    }
}


