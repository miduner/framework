<?php

namespace Midun\Console\Commands\Development;

use Midun\Console\Command;

class SyncCoreToFramework extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'development:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Sync core code to framework folder';

    /**
     * Others signature
     * 
     * @var array
     */
    protected array $otherSignatures = [
        "dev:sync"
    ];

    /**
     * Framework directory
     * 
     * @var string
     */
    protected string $frameworkDir;

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
        $frameworkDir = $this->getOption('dir') ?: config('develop.framework_dir');
        $developDir = base_path('vendor/miduner/miduner/src/Midun');

        if(!file_exists($frameworkDir)) {
        	$this->output->printError("Wrong given framework directory. Please check your configuration.\nThe given directory: `{$frameworkDir}`");
            exit(1);
        }

        exec("rm -rf {$frameworkDir}/src/Midun/");
        exec("cp -R {$developDir} {$frameworkDir}/src/Midun/");

        $this->output->printSuccess("Synced development directory to framework directory!\nPlease commit your code on {$frameworkDir}");
    }
}


