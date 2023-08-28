<?php

namespace Midun\Traits\Eloquent;

use PDO;
use PDOException;
use PDOStatement;
use Midun\Eloquent\ModelBindingObject;
use Midun\Database\QueryBuilder\QueryException;

trait ExecuteQuery
{
    /**
     * Execute sql
     *
     * @param string sql
     * @return mixed
     */
    public function request(string $sql)
    {
        try {
            $connection = app()->make('connection')->getConnection();
            log_query($sql);

            switch (true) {
                case $this->isInsertQuery($sql):
                    $object = $connection->query($sql);
                    break;
                case $this->isSelectQuery($sql):
                case $this->isUpdateQuery($sql):
                case $this->isDeleteQuery($sql):
                    $object = $connection->prepare($sql);
                    $this->bindingParams($object);
                    $object->execute();
                    break;
            }
            $this->rowCount = $object->rowCount();
            return $this->buildResponse($sql, $object, $connection);
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * Check is insert query
     * 
     * @param string $query
     * 
     * @return boolean
     */
    public function isInsertQuery(string $query): bool
    {
        $parse = explode(' ', $query);
        $queryType = array_shift($parse);

        return 'INSERT' === strtoupper(trim($queryType));
    }

    /**
     * Check is update query
     * 
     * @param string $query
     * 
     * @return boolean
     */
    public function isUpdateQuery(string $query): bool
    {
        $parse = explode(' ', $query);
        $queryType = array_shift($parse);

        return 'UPDATE' === strtoupper(trim($queryType));
    }
    /**
     * Check is select query
     * 
     * @param string $query
     * 
     * @return boolean
     */
    public function isSelectQuery(string $query): bool
    {
        $parse = explode(' ', $query);
        $queryType = array_shift($parse);

        return 'SELECT' === strtoupper(trim($queryType));
    }
    /**
     * Check is delete query
     * 
     * @param string $query
     * 
     * @return boolean
     */
    public function isDeleteQuery(string $query): bool
    {
        $parse = explode(' ', $query);
        $queryType = array_shift($parse);

        return 'DELETE' === strtoupper(trim($queryType));
    }

    /**
     * Building response
     * @param string $sql
     * @param PDOStatement $object
     * @param PDO $connection
     * 
     * @return mixed
     */
    private function buildResponse(string $sql, PDOStatement $object, PDO $connection)
    {
        $type = explode(" ", $sql);
        switch (array_shift($type)) {
            case 'SELECT':
                return $this->inCaseSelect($object);
            case 'INSERT':
                return $this->inCaseInsert($connection);
            case 'UPDATE':
                return true;
            case 'DELETE':
                return true;
            default:
                return $object;
        }
    }
    /**
     * Binding parameters to sql statements
     * 
     * @param PDOStatement $object
     * 
     * @return void
     */
    private function bindingParams(PDOStatement $object): void
    {
        if (!is_null($this->parameters)) {
            foreach ($this->parameters as $key => &$param) {
                $object->bindParam($key + 1, $param);
            }
        }
    }

    /**
     * Get one row has model instance
     * 
     * @param PDO $connection
     * 
     * @return mixed
     */
    private function getOneItemHasModel(PDO $connection)
    {
        $primaryKey = $this->getCalledModelInstance()->primaryKey();
        return $this->find($connection->lastInsertId(), $primaryKey);
    }

    private function getCalledModelInstance()
    {
        return new $this->calledFromModel;
    }

    /**
     * Exec sql get column id in connection
     *
     * @param PDO $connection
     * 
     * @return mixed
     */
    private function sqlExecGetColumnIdInConnection(PDO $connection)
    {
        $lastInsertId = $connection->lastInsertId();
        $getConfigFromConnection = app()->make('connection');
        $connection = $getConfigFromConnection->getConnection();
        $databaseName = $getConfigFromConnection->getConfig()[3];
        $newObject = $connection->prepare($this->createSqlStatementGetColumnName($databaseName));
        $newObject->execute();
        return $this->find($lastInsertId, $newObject->fetch()->COLUMN_NAME);
    }

    /**
     * Create sql statement get column name
     *
     * @param string $databaseName
     *
     * @return string
     */
    private function createSqlStatementGetColumnName(string $databaseName): string
    {
        return "
            SELECT
                COLUMN_NAME
            FROM
                INFORMATION_SCHEMA.COLUMNS
            WHERE
                TABLE_SCHEMA = '{$databaseName}' AND
                TABLE_NAME = '{$this->table}' AND EXTRA = 'auto_increment'
        ";
    }

    /**
     * Handle in case insert SQL
     *
     * @param PDO $connection
     * 
     * @return mixed
     */
    private function inCaseInsert(PDO $connection)
    {
        if (!empty($this->calledFromModel)) {
            return $this->getOneItemHasModel($connection);
        }
        return $this->sqlExecGetColumnIdInConnection($connection);
    }

    /**
     * Handle in case select SQL
     *
     * @param PDOStatement $object
     *
     */
    private function inCaseSelect(PDOStatement $object)
    {
        if ($this->find === true || $this->first === true) {
            if (!empty($this->calledFromModel)) {
                return $this->execBindingModelObject($object);
            }
            return $object->fetch();
        }
        if (!empty($this->calledFromModel)) {
            return $this->execBindingModelObject($object);
        }
        return $this->fetchOneItemWithoutModel($object);
    }

    /**
     * Fetch one item without model
     *
     * @param PDOStatement $object
     * 
     * @return array
     */
    private function fetchOneItemWithoutModel(PDOStatement $object): array
    {
        return $object->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Execute binding model object
     *
     * @param PDOStatement $pdoStatementObject
     *
     * @return object
     */
    private function execBindingModelObject(PDOStatement $pdoStatementObject): ?object
    {
        $resources = $pdoStatementObject->fetchAll(PDO::FETCH_CLASS, $this->calledFromModel);

        $binding = new ModelBindingObject($resources);

        return $binding->setTakeOne($this->find || $this->first)
            ->setTakeList(!$this->find && !$this->first)
            ->setIsThrow($this->isThrow)
            ->setArgs([
                'with' => $this->with
            ])
            ->verifyEmptyResources()
            ->handle();
    }
}
