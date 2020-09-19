<?php

namespace Midun\Eloquent\Relationship;

/**
 * Relationship class
 */
abstract class Relation
{
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
     * Get data of relationship
     * 
     * @param string $value
     * 
     * @return mixed
     */
    abstract public function getModelObject(string $value);

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
        if(!in_array($condition, $this->getAcceptCondition())) {
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
}