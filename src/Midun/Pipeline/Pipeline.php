<?php

namespace Midun\Pipeline;

use Closure;
use Midun\Container;
use Midun\Http\Request;
use Midun\Contracts\Pipeline\Pipeline as MainPipeline;

class Pipeline implements MainPipeline
{
    /**
     * The container implementation.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    protected Request $passable;

    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected array $pipes = [];

    /**
     * The method to call on each pipe.
     *
     * @var string
     */
    protected string $method = 'handle';

    /**
     * Create a new class instance.
     *
     * @param  \Midun\Container  $container
     * @return void
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container ?: Container::getInstance();
    }
    
    /**
     * Set the object being sent through the pipeline.
     *
     * @param  Request  $passable
     * @return $this
     */
    public function send(Request $passable): Pipeline
    {
        $this->passable = $passable;

        return $this;
    }

    /**
     * Set the array of pipes.
     *
     * @param  array|string  $pipes
     * @return $this
     */
    public function through($pipes): Pipeline
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * Set the method to call on the pipes.
     *
     * @param  string  $method
     * @return $this
     */
    public function via(string $method): Pipeline
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Run the pipeline with a final handleRouting callback.
     *
     * @param  \Closure  $handleRouting
     * @return mixed
     */
    public function then(Closure $handleRouting)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $this->prepareHandleRouting($handleRouting)
        );
        return $pipeline($this->passable);
    }

    /**
     * Get the final piece of the Closure onion.
     *
     * @param  \Closure  $handleRouting
     * @return \Closure
     */
    protected function preparehandleRouting(Closure $handleRouting): \Closure
    {
        return function () use ($handleRouting) {
            return $handleRouting();
        };
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function carry(): \Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {

                $pipe = Container::getInstance()->make($pipe);
                
                return $pipe->{$this->method}($passable, $stack);
            };
        };
    }
}
