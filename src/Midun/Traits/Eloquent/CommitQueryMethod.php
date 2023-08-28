<?php

namespace Midun\Traits\Eloquent;

use Midun\Http\Exceptions\AppException;

trait CommitQueryMethod
{
    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $this
     * @return mixed
     */
    public function get()
    {
        $sql = $this->pase();
        return $this->request($sql);
    }

    /**
     * View query builder to sql statement.
     *
     * @return void
     */
    public function toSql(): void
    {
        echo $this->pase();
        exit(0);
    }

    /**
     * Get full sql statement
     * 
     * @return string
     */
    public function getFullSql(): string
    {
        return $this->pase();
    }

    /**
     * Convert variables to sql
     *
     * @return string
     */
    public function pase(): string
    {
        if (!isset($this->table) || empty($this->table)) {
            return false;
        }
        $sql = $this->compile->compileSelect($this->distinct);
        $sql .= $this->compile->compileColumns($this->columns);
        $sql .= $this->compile->compileFrom($this->table);
        if (isset($this->joins) && is_array($this->joins)) {
            $sql .= $this->compile->compileJoins($this->joins);
        }
        if (isset($this->wheres) && is_array($this->wheres)) {
            $sql .= $this->compile->compileWheres($this->wheres);
        }
        if (isset($this->wherein)) {
            $sql .= $this->compile->compileWhereIn($this->wherein);
        }
        if (isset($this->groups) && is_array($this->groups)) {
            $sql .= $this->compile->compileGroups($this->groups);
        }
        if (isset($this->havings) && is_array($this->havings)) {
            $sql .= $this->compile->compileHavings($this->havings);
        }
        if (isset($this->orders) && is_array($this->orders)) {
            $sql .= $this->compile->compileOrders($this->orders);
        }
        if (isset($this->limit)) {
            $sql .= $this->compile->compileLimit($this->limit);
        }
        if (isset($this->offset)) {
            $sql .= $this->compile->compileOffset($this->offset);
        }

        return $sql;
    }

    /**
     * Create new record
     *
     * @param array data
     *
     * @return mixed
     */
    public function insert(array $data)
    {
        $sql = $this->compile->compileInsert($this->table, $data);
        return $this->request($sql);
    }

    /**
     * Create new record
     *
     * @param array data
     *
     * @return mixed
     * 
     * @throws AppException
     */
    public function create(array $data)
    {
        if (!empty($this->calledFromModel)) {
            $object = new $this->calledFromModel;
            $fillable = $object->fillable();
            $hidden = $object->hidden();
            $columns = array_merge($fillable, $hidden);
            $sql = $this->compile->compileCreate($object, $columns, $data);

            return $this->request($sql);
        }
        throw new AppException("Method 'create' doesn't exists");
    }

    /**
     * Find 1 record usually use column id
     *
     * @param string value
     * @param string column
     * @return mixed
     */
    public function find(string $value, string $column = 'id')
    {
        $this->find = true;
        $this->limit = 1;
        $this->where($column, '=', $value);
        $sql = $this->compile->compileSelect($this->distinct);
        $sql .= $this->compile->compileColumns($this->columns);
        $sql .= $this->compile->compileFrom($this->table);
        $sql .= $this->compile->compileWheres($this->wheres);
        return $this->request($sql);
    }

    /**
     * Find 1 record usually use column id
     *
     * @param string value
     * @param string column
     * @return mixed
     */
    public function findOrFail(string $value, string $column = 'id')
    {
        $this->find = true;
        $this->limit = 1;
        $this->isThrow = true;
        $this->where($this->calledFromModel ? $this->getCalledModelInstance()->primaryKey() : $column, '=', $value);
        $sql = $this->compile->compileSelect($this->distinct);
        $sql .= $this->compile->compileColumns($this->columns);
        $sql .= $this->compile->compileFrom($this->table);
        $sql .= $this->compile->compileWheres($this->wheres);
        return $this->request($sql);
    }

    /**
     * First 1 record usually use column id
     *
     * @return mixed
     */
    public function first()
    {
        $this->first = true;
        $this->limit = 1;
        $sql = $this->pase();
        return $this->request($sql);
    }

    /**
     * First 1 record usually use column id
     *
     * @return mixed
     */
    public function firstOrFail()
    {
        $this->first = true;
        $this->isThrow = true;
        $this->limit = 1;
        $sql = $this->pase();
        return $this->request($sql);
    }
    /**
     * Quick login with array params
     *
     * @param array data
     * @return mixed
     */
    public function login(array $data)
    {
        $this->find = true;
        $sql = $this->compile->compileLogin($this->table, $data);
        return $this->request($sql);
    }

    /**
     * Destroy a record from condition
     *
     * @return mixed
     */
    public function delete()
    {
        $sql = $this->compile->compileDelete($this->table);

        if (!is_null($this->existsModelInstance)) {
            $model = $this->existsModelInstance;
            $primaryKey = $model->primaryKey();
            $valueKey = $model->$primaryKey;
            $this->where($primaryKey, $valueKey);
        }
        $sql .= $this->compile->compileWheres($this->wheres);

        return $this->request($sql);
    }

    /**
     * Update records from condition
     *
     * @param array data
     * @return mixed
     */
    public function update(array $data)
    {
        $sql = $this->compile->compileUpdate($this->table, $data);

        if (!is_null($this->existsModelInstance)) {
            $model = $this->existsModelInstance;
            $primaryKey = $model->primaryKey();
            $valueKey = $model->$primaryKey;
            $this->where($primaryKey, $valueKey);
            $sql .= $this->compile->compileWheres($this->wheres);
        }

        return $this->request($sql);
    }

    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return app()->make('connection')->getConnection()->{__FUNCTION__}();
    }

    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit(): bool
    {
        return app()->make('connection')->getConnection()->{__FUNCTION__}();
    }

    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollBack(): bool
    {
        return app()->make('connection')->getConnection()->{__FUNCTION__}();
    }
}
