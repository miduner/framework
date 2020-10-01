<?php

namespace Midun\Console\Commands;

use Midun\Console\Command;

class CreateServerCliCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Creating cli server';

    /**
     * Other called signatures
     */
    protected array $otherSignatures = [
        'ser',
        'serv',
        'server',
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
        $host = '127.0.0.1';
        $port = '8000';
        $open = false;
        foreach ($this->argv() as $key => $param) {
            switch (true) {
                case strpos($param, '--host=') !== false:
                    $host = str_replace('--host=', '', $param);
                    break;
                case strpos($param, '-h') !== false:
                    $host = $this->argv()[$key + 1];
                    break;
                case strpos($param, '--port=') !== false:
                    $port = str_replace('--port=', '', $param);
                    break;
                case strpos($param, '-p') !== false:
                    $port = $this->argv()[$key + 1];
                    break;
                case strpos($param, '-o') !== false:
                case strpos($param, '--open') !== false:
                    $open = true;
                    break;
                case strpos($param, '--route=') !== false:
                    $route = str_replace('--route=', '', $param);
                    break;
                case strpos($param, '-r') !== false:
                    $route = $this->argv()[$key + 1];
                    break;
            }
        }
        if ($open) {
            if (isset($route)) {
                $routes = app()->make('route')->collect();
                $routeFound = array_filter($routes, fn ($rt) => $rt->getName() === $route);
                if (empty($routeFound)) {
                    $this->output->printError("Route `{$route}` not found");
                    exit(1);
                }
                $uri = array_shift($routeFound)->getUri();
            }
            $url = "http://{$host}:{$port}" . (isset($uri) ? $uri : '');
            switch (true) {
                case app()->isWindows():
                    exec("explorer $url");
                    break;
                default:
                    exec("open $url");
            }
        } else {
            $this->output->printSuccess("Using argument --open to open server on browser.");
        }
        $this->output->printSuccess("Starting development at: http://{$host}:{$port}");
        system("php -S {$host}:{$port} server.php");
    }
}
