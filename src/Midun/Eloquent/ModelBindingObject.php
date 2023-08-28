<?php

namespace Midun\Eloquent;

use Midun\Eloquent\Relationship\Relation;

final class ModelBindingObject
{
    /**
     * Model is binding object
     * 
     * @var Model
     */
    private Model $model;

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
    private ?object $resource = null;

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
     * 
     * @param array $resources
     */
    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * Get with resource
     * 
     * @param Model $object
     * @param string $with
     * @param string|\Closure $callback
     * 
     * @return Model
     */
    public function getWith(Model &$object, string $with, $callback): Model
    {
        if (is_numeric($with)) {
            $with = $callback;
        }
        if (!method_exists($object, $with)) {
            throw new EloquentException("Method `{$with}()` not found in class {$this->model}");
        }
        $relation = $object->$with();

        $localKey = $relation->getLocalKey();
        $relationData = $relation->{Relation::METHOD_EXECUTION}(
            $object->$localKey, $callback instanceof \Closure ? $callback : null
        );
        $object->$with = $relationData;

        return $object;
    }

    /**
     * Binding one resource object
     *
     * @param Model $object
     *
     * @return Model
     *
     * @throws EloquentException
     */
    private function bindOne(Model $object): Model
    {
        if ($this->hasWith()) {
            foreach ($this->withes() as $with => $callback) {
                $this->getWith($object, $with, $callback);
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
            if ($this->hasWith()) {
                foreach ($this->withes() as $with => $callback) {
                    $this->getWith($resource, $with, $callback);
                }
            }
        }

        return new Collection($resources);
    }

    /**
     * Set take one
     *
     * @param bool $oneOf
     *
     * @return self
     */
    public function setTakeOne(bool $oneOf): self
    {
        $this->oneOf = $oneOf;

        return $this;
    }

    /**
     * Set take list
     *
     * @param bool $listOf
     *
     * @return self
     */
    public function setTakeList(bool $listOf): self
    {
        $this->listOf = $listOf;

        return $this;
    }

    /**
     * Set arguments
     *
     * @param array $args
     *
     * @return self
     */
    public function setArgs(array $args): self
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Set is throw
     *
     * @param bool $isThrow
     *
     * @return self
     */
    public function setIsThrow(bool $isThrow): self
    {
        $this->isThrow = $isThrow;

        return $this;
    }

    /**
     * Verify empty resources
     *
     * @return self
     */
    public function verifyEmptyResources(): self
    {
        $this->checkThrow();

        if ($this->isGetOne()) {
            $rc = $this->getResources();
            $this->setResource(
                array_shift($rc)
            );
        }

        return $this;
    }

    /**
     * Set resource bind one
     * 
     * @param Model $resource
     * 
     * @return void
     */
    public function setResource(?Model $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * Check is get one
     * 
     * @return bool
     */
    public function isGetOne(): bool
    {
        return $this->oneOf;
    }

    /**
     * Check is get list
     * 
     * @return bool
     */
    public function isGetList(): bool
    {
        return $this->listOf;
    }

    /**
     * Get list of resources
     * 
     * @return array
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Get resource bind one
     * 
     * @return Model
     */
    public function getResource(): ?Model
    {
        return $this->resource;
    }

    /**
     * Get is throws an exception
     * 
     * @return bool
     */
    public function isThrows(): bool
    {
        return $this->isThrow;
    }

    /**
     * Check is empty resources
     * 
     * @return bool
     */
    public function isEmptyResources(): bool
    {
        return empty($this->getResources());
    }

    /**
     * Check can throw
     * 
     * @return bool
     */
    public function canThrow(): bool
    {
        return $this->isEmptyResources()
        && $this->isGetOne()
        && !$this->isGetList()
        && $this->isThrows();
    }

    /**
     * Check is throw and throw the exception
     * 
     * @return void
     * 
     * @throws EloquentException
     */
    public function checkThrow(): void
    {
        if ($this->canThrow()) {
            throw new EloquentException("Resource not found", 404);
        }
    }

    /**
     * Execute condition and directional
     *
     * @return mixed
     */
    public function handle()
    {
        switch (true) {
            case $this->isGetOne():
                if (is_null($this->getResource())) {
                    return null;
                }
                return $this->bindOne(
                    $this->getResource()
                );
            case $this->isGetList():
                return $this->bindMultiple(
                    $this->getResources()
                );
        }
    }

    /**
     * Check has with method
     * 
     * @return bool
     */
    private function hasWith(): bool
    {
        return !empty($this->withes());
    }

    /**
     * Get list of with
     * 
     * @return array
     */
    private function withes(): array
    {
        return $this->args['with'] ??= [];
    }
}
