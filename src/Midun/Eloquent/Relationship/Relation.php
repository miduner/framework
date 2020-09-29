<?php

namespace Midun\Eloquent\Relationship;

use Midun\Container;
use Midun\Eloquent\Model;

/**
 * Relationship class
 */
abstract class Relation
{
    /**
     * Model that is belongs to this
     * 
     * @var string
     */
    protected string $model;

    /**
     * Instance of model
     * 
     * @var Model
     */
    protected Model $instance;

    /**
     * Where condition
     * 
     * @var array
     */
    protected array $wheres = [];

    /**
     * List of where conditions
     * 
     * @var array
     */
    protected array $conditions = [
        "=", ">=", "<=", "<", ">", "<>"
    ];

    /**
     * Method execute
     * 
     * @var string
     */
    const METHOD_EXECUTION = 'getModelObject';

    /**
     * Get data of relationship
     * 
     * @param string $value
     * @param \Closure $callback
     * 
     * @return mixed
     */
    abstract public function getModelObject(string $value, ?\Closure $callback = null);

    /**
     * Set where condition
     * 
     * @param string $column
     * @param string $condition
     * @param string $value
     * 
     * @return Relation
     */
    public function where(string $column, string $condition = "=", string $value = ""): Relation
    {
        if (!in_array($condition, $this->getAcceptCondition())) {
            $where = [$column, "=", $condition];
        } else {
            $where = [$column, $condition, $value];
        }
        $this->wheres = [...$this->getWhereCondition(), $where];

        return $this;
    }

    /**
     * Get list of accept conditions
     * 
     * @return array 
     */
    protected function getAcceptCondition(): array
    {
        return $this->conditions;
    }

    /**
     * Get where condition
     * 
     * @return array
     */
    public function getWhereCondition(): array
    {
        return $this->wheres;
    }

    /**
     * Set model instance
     * 
     * @param Model $instance
     * 
     * @return void
     */
    protected function setModelInstance(Model $instance): void
    {
        $this->instance = $instance;
    }

    /**
     * Get model instance
     * 
     * @return Model
     */
    protected function getModelInstance(): Model
    {
        return $this->instance;
    }

    /**
     * Set the model
     * 
     * @param string $model
     * 
     * @return void
     */
    protected function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * Get the model
     * 
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
