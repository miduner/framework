<?php

namespace Midun\Console;

use Midun\Container;
use Midun\Supports\ConsoleOutput;
use Midun\Contracts\Console\Command as CommandContract;

abstract class Command implements CommandContract
{
	/**
	 * Output of command
	 *
	 * @var ConsoleOutput
	 */
	protected ConsoleOutput $output;

	/**
	 * @var Container
	 */
	protected Container $app;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = "";

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = "";

    /**
     * Options in options
     * 
     * @var array
     */
    protected array $options = [];

    /**
     * Argv
     * 
     * @var array
     */
    protected array $argv = [];

    /**
     * Flag is using cache
     * 
     * @var bool
     */
    protected bool $usingCache = true;

    /**
     * Other called signatures
     * 
     * @var array
     */
    protected array $otherSignatures = [];

    /**
     * Option required
     * 
     * @var array
     */
    protected array $required = [];

    /**
     * True format for command
     * 
     * @var string
     */
    protected string $format = "";

    /**
     * Helper for using command
     * 
     * @var string
     */
    protected string $helper = "";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        global $argv;

        $this->output = new ConsoleOutput;
        $this->setArgv($argv);
        $this->app = Container::getInstance();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    abstract public function handle(): void;

    /**
     * Set argv
     * 
     * @param array $argv
     * 
     * @return void
     */
    public function setArgv(array $argv = []): void
    {
        $this->argv = $argv;
    }

    /**
     * Get argv
     * 
     * @return array
     */
    public function argv(): array
    {
        return $this->argv;
    }

    /**
     * Get signature
     * 
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * Get other signatures
     * 
     * @return array
     */
    public function getOtherSignatures(): array
    {
        return $this->otherSignatures;
    }

    /**
     * Get description
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set options of passed command
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options): void
    {
        $results = [];

        foreach ($options as $opKey => $option) {
            if (strpos($option, '--') !== false) {
                $parsing = explode('=', str_replace('--', '', $option));
                $key = array_shift($parsing);
                $results[$key] = $key == 'help' ? true : array_shift($parsing);
            } else {
                $results[$opKey] = $option;
            }
        }

        $this->options = $results;
    }

    /**
     * Get options of passed command
     * 
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get options of passed command
     * 
     * @param string $property
     * 
     * @return string
     */
    public function getOption(string $property): ?string
    {
        return isset($this->options[$property]) ? $this->options[$property] : null;
    }

    /**
     * Get check is using cache
     * 
     * @return boolean
     */
    public function isUsingCache(): bool
    {
        return $this->usingCache;
    }

    /**
     * Check in is verified
     * 
     * @return bool
     */
    public function isVerified(): bool
    {
        foreach ($this->required as $required) {
            if (!isset($this->options[$required])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get format of command
     * 
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get helper using command
     * 
     * @return string
     */
    public function getHelper(): string
    {
        return $this->helper;
    }
}
