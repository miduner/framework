<?php

namespace Midun\Database\QueryBuilder;

use Midun\Traits\Eloquent\Pagination;
use Midun\Traits\Eloquent\ExecuteQuery;
use Midun\Database\QueryBuilder\Compile;
use Midun\Traits\Eloquent\CommitQueryMethod;
use Midun\Traits\Eloquent\HandleCompileWithBuilder;

class QueryBuilder
{
    use HandleCompileWithBuilder, ExecuteQuery, Pagination, CommitQueryMethod;

    /**
     * The list of accept operator using in query builder
     */
    private array $operator = [
        '=', '<>', '>', '<', '<=', '>=',
    ];

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    private array $columns = ["*"];

    /**
     * The list of need bindings arguments
     * 
     * @var array
     */
    private array $parameters = [];

    /**
     * The table which the query is targeting.
     *
     * @var string
     */
    private string $table;

    /**
     * Indicates if the query returns distinct results.
     *
     * @var bool
     */
    private bool $distinct = false;

    /**
     * The table joins for the query.
     *
     * @var array
     */
    private array $joins = [];

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    private array $wheres = [];

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    private array $wherein = [];

    /**
     * The groupings for the query.
     *
     * @var array
     */
    private array $groups = [];

    /**
     * The having constraints for the query.
     *
     * @var array
     */
    private array $havings = [];

    /**
     * The orderings for the query.
     *
     * @var array
     */
    private array $orders = [];

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    private int $limit = 0;

    /**
     * The number of records to skip.
     *
     * @var int
     */
    private int $offset = 0;

    /**
     * Take 1 record
     *
     * @var boolean
     */
    private bool $find = false;

    /**
     * Take 1 record
     *
     * @var boolean
     */
    private bool $first = false;

    /**
     * Fails throw Exception
     * 
     * @var bool
     */
    private bool $isThrow = false;

    /**
     * Flag checking pagination
     * 
     * @var bool
     */
    private bool $isPagination = false;

    /**
     * Count row execute
     * 
     * @var int
     */
    private int $rowCount = 0;

    /**
     * Compile instance
     * 
     * @var Compile
     */
    private Compile $compile;

    /**
     * Class called
     * 
     * @var string
     */
    private string $calledFromModel = "";

    /**
     * Create a new query builder instance.
     *
     * @param  ConnectionInterface  $this->table
     * @return void
     */
    public function __construct(string $table = "", $calledClass = "")
    {
        $this->calledFromModel = $calledClass;
        $this->table = $table;
        $this->compile = new Compile;
    }

    /**
     * Set the table which the query is targeting.
     *
     * @param  string  $table
     * @return self
     */
    public static function table(string $table): QueryBuilder
    {
        return new self($table);
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array|mixed  $columns
     * @return self
     */
    public function select($columns): QueryBuilder
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add a new select column to the query.
     *
     * @param  array|mixed  $column
     * @return self
     */
    public function addSelect($column): QueryBuilder
    {
        $column = is_array($column) ? $column : func_get_args();

        $this->columns = [...$this->columns, ...$column];

        return $this;
    }

    /**
     * Force the query to only return distinct results.
     *
     * @return self
     */
    public function distinct(): QueryBuilder
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Add a join clause to the query.
     *
     * @param  string  $tableJoin
     * @param  string  $first
     * @param  string  $operator
     * @param  string  $second
     * @param  string  $type
     * @return self
     */
    public function join(string $tableJoin, string $st, string $operator, string $nd, string $type = 'INNER'): QueryBuilder
    {
        $this->joins[] = [$tableJoin, $st, $operator, $nd, $type];
        return $this;
    }

    /**
     * Add a left join to the query.
     *
     * @param  string  $tableJoin
     * @param  string  $first
     * @param  string  $operator
     * @param  string  $second
     * @return \Database\QueryBuilder|static
     */
    public function leftJoin(string $tableJoin, string $st, string $operator, string $nd): QueryBuilder
    {
        return $this->join($tableJoin, $st, $operator, $nd, 'LEFT');
    }

    /**
     * Add a right join to the query.
     *
     * @param  string  $tableJoin
     * @param  string  $first
     * @param  string  $operator
     * @param  string  $second
     * @return \Database\QueryBuilder|static
     */
    public function rightJoin(string $tableJoin, string $st, string $operator, string $nd): QueryBuilder
    {
        return $this->join($tableJoin, $st, $operator, $nd, 'RIGHT');
    }

    /**
     * Check condition and execute statement
     * 
     * @param bool $condition
     * @param \Closure $callback
     * @param \Closure $default
     */
    public function when($condition, $callback, ?\Closure $default = null): QueryBuilder
    {
        if ($condition) {
            $callback($this);
        } elseif (!is_null($default)) {
            $default($this);
        }
        return $this;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return self
     */
    public function where($column, string $operator, $value = null, $boolean = 'AND'): QueryBuilder
    {
        if (!is_callable($column) && !is_array($column)) {
            if (!in_array($operator, $this->operator)) {
                $value = $operator;
                $operator = '=';
            }
            $this->parameters[] = $value;
            $this->wheres[] = [$column, $operator, $value, $boolean];
            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->parameters[] = $value;
                $this->wheres[] = [$key, '=', $value, $boolean];
            }
            return $this;
        }

        $this->wheres[] = ['start_where'];
        call_user_func_array($column, [$this]);
        $this->wheres[] = ['end_where'];
        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string|array|\Closure  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return self
     */
    public function orWhere($column, string $operator, $value = null): QueryBuilder
    {
        if (!is_callable($column)) {
            return $this->where($column, $operator, $value, 'OR');
        }
        $this->wheres[] = ['start_or'];
        call_user_func_array($column, [$this]);
        $this->wheres[] = ['end_or'];
        return $this;
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  bool    $in
     * @return self
     */
    public function whereIn(string $column, array $value = [], bool $in = true): QueryBuilder
    {
        $this->wherein = [$column, $value , $in];
        return $this;
    }

    /**
     * Add a "where not in" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @return self
     */
    public function whereNotIn(string $column, array $value = []): QueryBuilder
    {
        return $this->whereIn($column, $value, false);
    }

    /**
     * Add a "group by" clause to the query.
     *
     * @param  string|array  $groups
     * @return self
     */
    public function groupBy($groups): QueryBuilder
    {
        $this->groups = is_array($groups) ? $groups : func_get_args();
        return $this;
    }

    /**
     * Add a "having" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  string  $value
     * @param  string  $boolean
     * @return self
     */
    public function having(string $column, string $operator, string $value, string $boolean = 'and'): QueryBuilder
    {
        $this->havings[] = [$column, $operator, $value, $boolean];
        return $this;
    }

    /**
     * Add a "or having" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  string  $value
     * @return self
     */
    public function orHaving(string $column, string $operator, string $value): QueryBuilder
    {
        return $this->having($column, $operator, $value, 'or');
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param  string  $column
     * @param  string  $direction
     * @return self
     */
    public function orderBy(string $columns, string $type = 'asc'): QueryBuilder
    {
        $this->orders[] = [$columns, $type];
        return $this;
    }

    /**
     * Add a descending "order by" clause to the query.
     *
     * @param  string  $column
     * @return self
     */
    public function orderByDesc(string $column = "id"): QueryBuilder
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  string  $column
     * @return self
     */
    public function latest(string $column = 'id'): QueryBuilder
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  string  $column
     * @return self
     */
    public function oldest($column = 'id'): QueryBuilder
    {
        return $this->orderBy($column, 'asc');
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param  int  $value
     * @return self
     */
    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int  $value
     * @return self
     */
    public function take(int $value): QueryBuilder
    {
        return $this->limit($value);
    }

    /**
     * Alias to set the "offset" value of the query.
     *
     * @param  int  $value
     * @return self
     */
    public function skip(int $value): QueryBuilder
    {
        return $this->offset($value);
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param  int  $value
     * @return self
     */
    public function offset(int $offset): QueryBuilder
    {
        $this->offset = $offset;
        return $this;
    }
}
