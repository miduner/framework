<?php

namespace Midun\Eloquent;

final class ModelBindingObject
{
    /**
     * Flag checking binding one resource
     * 
     * @var bool
     */
    private bool $oneOf = false;

    /**
     * Flag checking binding list resource
     * 
     * @var bool
     */
    private bool $listOf = false;

    /**
     * List instance object model binding
     * 
     * @var array
     */
    private array $resources = [];

    /**
     * List instance object model binding
     * 
     * @var object
     */
    private object $resource;

    /**
     * Model binding
     */
    private string $model = "";

    /**
     * List of args
     */
    private array $args = [];

    /**
     * Flag checking is throwable
     */
    private bool $isThrow = false;

    /**
     * Initial constructor
     */
    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * Receive parameters
     *
     * @param mixed list of parameters
     * 
     * @return mixed
     */
    public function receive()
    {
        list($oneOf, $listOf, $model, $args, $isThrow) = func_get_args();
        $this->oneOf = $oneOf;
        $this->listOf = $listOf;
        $this->model = $model;
        $this->args = $args;
        $this->isThrow = $isThrow;
        return $this->checkEmpty();
    }

    /**
     * Checking empty resources
     * 
     * @return object
     */
    private function checkEmpty()
    {
        if (empty($this->resources)) {
            if ($this->oneOf && !$this->listOf && $this->isThrow) {
                throw new EloquentException("Resource not found", 404);
            }
        }
        if ($this->oneOf && !$this->listOf) {
            $this->resource = array_shift($this->resources);
        }
        return $this->handle();
    }

    /**
     * Execute condition and directional
     * 
     * @return mixed
     */
    private function handle()
    {
        if ($this->oneOf) {
            return $this->bindOne($this->resource);
        }
        if ($this->listOf) {
            return $this->bindMultiple($this->resources);
        }
    }

    /**
     * Binding one resource object
     *
     * @param Model $object
     *
     * @return Model
     */
    private function bindOne(Model $object): Model
    {
        if (isset($this->args['with']) && !empty($this->args['with'])) {
            foreach ($this->args['with'] as $with) {
                if (method_exists($object, $with)) {
                    $object->$with = $object->$with();
                } else {
                    throw new EloquentException("Method '{$with}' not found in class {$this->model}");
                }
            }
        }
        $object->callServiceHidden();
        return $object;
    }

    /**
     * Binding multiple resource objects
     *
     * @param array $resources
     *
     * @return Collection
     */
    private function bindMultiple(array $resources): Collection
    {
        foreach ($resources as $resource) {
            $resource->callServiceHidden();
            if (!empty($this->args['with'])) {
                foreach ($this->args['with'] as $with) {
                    if (method_exists($resource, $with)) {
                        $resource->$with = $resource->$with();
                    } else {
                        throw new EloquentException("Method '{$with}()' not found in class {$this->model}");
                    }
                }
            }
        }

        return new Collection($resources);
    }
}
