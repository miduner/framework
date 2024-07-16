<?php

namespace Midun\Eloquent\Relationship;

use Midun\Database\QueryBuilder\QueryBuilder;
use Midun\Eloquent\Model;

class HasOneRelation extends Relation
{
    /**
     * Local key
     *
     * @var string
     */
    protected string $localKey;

    /**
     * Local key valueHasOneRelation
     *
     * @var string
     */
    protected string $localValue;

    /**
     * Remote key
     *
     * @var string
     */
    protected string $remoteKey;

    /**
     * Initial constructor of HasOneRelation
     *
     * @param string $model
     * @param string $localKey
     * @param string $remoteKey
     *
     * @return void
     */
    public function __construct(string $model, string $localKey = "", string $remoteKey = "", Model $instance)
    {
        $this->setModel($model);
        $this->setLocalKey($localKey);
        $this->setRemoteKey($remoteKey);
        $this->setModelInstance($instance);
        $this->setLocalValue(
            $this->getModelInstance()->{$this->getLocalKey()}
        );
    }

    /**
     * Set the local key
     *
     * @param string $localKey
     *
     * @return void
     */
    protected function setLocalKey(string $localKey): void
    {
        if (empty($localKey)) {
            $model = $this->getModel();
            $localKey = (new $model)->primaryKey();
        }
        $this->localKey = $localKey;
    }

    /**
     * Set local value
     *
     * @param string $localValue
     *
     * @return void
     */
    public function setLocalValue(string $localValue): void
    {
        $this->localValue = $localValue;
    }

    /**
     * Get local value
     *
     * @return string
     */
    public function getLocalValue()
    {
        return $this->localValue;
    }

    /**
     * Set the remote key
     *
     * @param string $remoteKey
     *
     * @return void
     */
    protected function setRemoteKey(string $remoteKey): void
    {
        if (empty($remoteKey)) {
            $remoteKey = $this->getLocalKey();
        }
        $this->remoteKey = $remoteKey;
    }

    /**
     * Get local key
     *
     * @return string
     */
    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    /**
     * Get remote key
     *
     * @return string
     */
    public function getRemoteKey(): string
    {
        return $this->remoteKey;
    }

    /**
     * Execute get data
     *
     * @param string $value
     *
     * @return Model
     */
    public function getModelObject(string $value, ?\Closure $callback = null): ?Model
    {
        $builder = $this->buildWhereCondition($value);

        if (!is_null($callback)) {
            $callback($builder);
        }

        return $builder->first();
    }

    /**
     * Call function
     *
     * @param string $method
     * @param array $args
     *
     * @return QueryBuilder
     */
    public function __call(string $method, array $args)
    {
        return $this->buildWhereCondition(
            $this->getLocalValue()
        )->$method(...$args);
    }

    /**
     * Build where condition
     *
     * @param string $value
     *
     * @return QueryBuilder
     */
    protected function buildWhereCondition(string $value)
    {
        $builder = app()->make($this->getModel())
            ->where($this->getLocalKey(), $value);

        foreach ($this->getWhereCondition() as $where) {
            $builder->where(array_shift($where), array_shift($where), array_shift($where));
        }

        return $builder;
    }
}
