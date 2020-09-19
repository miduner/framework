<?php

namespace Midun\Eloquent\Relationship;

use Midun\Eloquent\Model;

class HasOneRelation extends Relation
{
    /**
     * Model that is belongs to this
     * 
     * @var string
     */
    protected string $model;

    /**
     * Local key 
     * 
     * @var string
     */
    protected string $localKey;

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
    public function __construct(string $model, string $localKey = "", string $remoteKey = "")
    {
        $this->setModel($model);
        $this->setLocalKey($localKey);
        $this->setRemoteKey($remoteKey);
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
     * Set the local key
     * 
     * 
     * @param string $localKey
     * 
     * @return void
     */
    protected function setLocalKey(string $localKey): void
    {
        if(empty($localKey)) {
            $model = $this->getModel();
            $localKey = (new $model)->primaryKey();
        }
        $this->localKey = $localKey;
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
        if(empty($remoteKey)) {
            $remoteKey = $this->getLocalKey();
        }
        $this->remoteKey = $remoteKey;
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
        return $this->remoteKe;
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
        $builder = app()->make($this->getModel())
            ->where($this->getLocalKey(), $value);

        foreach($this->getWhereCondition() as $where) {
            $builder->where(array_shift($where), array_shift($where), array_shift($where));
        }

        if(!is_null($callback)) {
            $callback($builder);
        }

        return $builder->first();
    }
}