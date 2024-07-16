<?php

namespace Midun\Database\Connections\Mysql;

use PDO;
use PDOException;
use PDOStatement;

class MysqlPdo extends PDO
{
    /**
     * Initial MysqlPdo
     * 
     * @param string $dns
     * @param string $username
     * @param string $password
     * @param array $options
     * 
     * @return mixed
     */
    public function __construct($dns, $username, $password, $options = [])
    {
        parent::__construct($dns, $username, $password, $options);
    }

    /**
     * Prepares a statement for execution and returns a statement object
     * 
     * @param string $statement
     * @param array $options 
     * 
     * @return PDOStatement|true
     */
    public function prepare($statement, $options = []): PDOStatement
    {
        return parent::prepare($statement, $options);
    }

    /**
     * Execute a statement prepared
     * 
     * @return bool
     */
    public function execute(): bool
    {
        return parent::execute();
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     * 
     * @param string $statement
     * 
     * @return PDOStatement|false
     */
    public function query(string $query, int $fetchMode = null, ...$fetch_mode_args): PDOStatement
    {
        return parent::query($query, $fetchMode, $fetch_mode_args);
    }

    /**
     * Return the number of affected rows
     * 
     * @return int
     */
    public function rowCount(): int
    {
        return parent::rowCount();
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     * 
     * @param string $statement
     * 
     * @return int|false
     */
    public function exec($statement): int
    {
        return parent::exec($statement);
    }

    /**
     * Turns off autocommit mode. While autocommit mode is turned off,
     * changes made to the database via the PDO object instance are not
     * committed until you end the transaction by calling {@link PDO::commit()}.
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return parent::beginTransaction();
    }

    /**
     * Commits a transaction
     * 
     * @return bool
     * 
     * @throws PDOException
     */
    public function commit(): bool
    {
        return parent::commit();
    }

    /**
     * Rolls back a transaction
     * 
     * @return bool
     * 
     * @throws PDOException
     */
    public function rollBack(): bool
    {
        return parent::rollBack();
    }
}
