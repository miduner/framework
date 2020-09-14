<?php

namespace Midun\Traits\Eloquent;

use Midun\Eloquent\Model;
use Midun\Eloquent\EloquentException;
use Midun\Http\Exceptions\AppException;
use Midun\Database\QueryBuilder\QueryException;

trait HandleCompileWithBuilder
{
    /**
     * Instance of exists model
     * 
     * @var Model
     */
    protected ?Model $existsModelInstance = null;

    /**
     * List of with relations
     */
    public array $with = [];

    /**
     * Create new query builder from model
     *
     * @param string $table
     * @param array $modelMeta
     * @param string $method
     * @param array $args
     * @param Model $instance
     * 
     * @return mixed
     * 
     * @throws QueryException
     */
    public function staticEloquentBuilder(string $table, array $modelMeta, string $method, ?array $args = null, ?Model $instance = null)
    {
        try {
            $object = isset($modelMeta['calledClass']) ? new self($table, $modelMeta['calledClass']) : new self($table);
            switch ($method) {
                case 'find':
                case 'findOrFail':
                    try {
                        list($value) = $args;
                        return $object->$method($value, $modelMeta['primaryKey']);
                    } catch (\TypeError $e) {
                        throw new \Exception($e->getMessage());
                    }
                case 'with':
                    $object->with = $args && is_array($args[0]) ? $args[0] : $args;
                    return $object;
                case 'update':
                case 'delete':
                    $object->existsModelInstance = $instance;
                default:
                    try {
                        if (method_exists($object, $method)) {
                            return $object->$method(...$args);
                        }
                        $buildScope = $this->_getScopeMethod($method);
                        $objectModel = new $modelMeta['calledClass'];
                        if (method_exists($objectModel, $buildScope)) {
                            return $objectModel->$buildScope($object, ...$args);
                        }
                        throw new AppException("Method `{$method}` does not exist");
                    } catch (\TypeError $e) {
                        throw new \Exception($e->getMessage());
                    }
            }
        } catch (\Exception $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * Handle call
     * 
     * @param string $method
     * @param array $args
     * 
     * @return Model
     * 
     * @throws EloquentException
     */
    public function __call(string $method, array $args): Model
    {
        try {
            $buildScope = $this->_getScopeMethod($method);
            array_unshift($args, $this);
            return (new $this->calledFromModel)->$buildScope(...$args);
        } catch (\TypeError $e) {
            throw new EloquentException($e->getMessage());
        }
    }

    /**
     * Make scope method
     * 
     * @param string $method
     * 
     * @return string
     */
    private function _getScopeMethod($method): string
    {
        return 'scope' . ucfirst($method);
    }

    /**
     * Set with option
     * 
     * @param string|array $with
     * 
     * @return self
     */
    public function with($with): self
    {
        $this->with = is_array($with) ? $with : func_get_args();
        return $this;
    }
}
