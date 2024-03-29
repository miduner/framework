<?php

namespace Midun\Console\Commands\Make;

use Midun\Console\Command;

class MakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'make:controller';

    /**
     * The console controller description.
     *
     * @var string
     */
    protected string $description = 'Making controller service';

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
        $options = $this->getOptions();

        $name = array_shift($options);

        $paseController = explode('/', $name);
        $namespace = ';';
        $fullDir = base_path('app/Http/Controllers/');
        if (count($paseController) > 1) {
            $controller = array_pop($paseController);
            $namespace = '\\' . implode("\\", $paseController) . ';';
            foreach ($paseController as $dir) {
                $fullDir .= "{$dir}";
                if (!is_dir($fullDir)) {
                    @mkdir($fullDir, 0777, true);
                }
                $fullDir .= '/';
            }
        } else {
            $controller = $name;
        }
        $defaultControllerPath = base_path('vendor/miduner/miduner/src/Midun/Helpers/Init/controller.txt');
        $defaultController = file_get_contents($defaultControllerPath);
        $defaultController = str_replace(':namespace', $namespace, $defaultController);
        $defaultController = str_replace(':controller', $controller, $defaultController);
        $needleController = "{$fullDir}$controller.php";
        if (!file_exists($needleController)) {
            $myfile = fopen($needleController, "w") or die("Unable to open file!");
            fwrite($myfile, $defaultController);
            fclose($myfile);
            $this->output->printSuccess("Created controller {$controller}");
        } else {
            $this->output->printWarning("Controller {$needleController} already exists");
        }
    }
}
