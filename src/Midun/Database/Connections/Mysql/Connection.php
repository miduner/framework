<?php

namespace Midun\Database\Connections\Mysql;

use \PDO;
use \PDOException;
use Midun\Database\Connections\Connection as MidunConnection;

class Connection extends MidunConnection
{
    /**
     * Reset driver
     * 
     * @param string $driver
     *
     * @return void
     * 
     * @throws MysqlConnectionException
     */
    public function setDriver(string $driver): void
    {
        $connections = config("database.connections");
        if (!isset($connections[$driver])) {
            throw new MysqlConnectionException("Couldn't find driver {$driver}");
        }
        $this->driver = $driver;
        $this->makeInstance();
    }

    /**
     * Check the connection is available
     * 
     * @return boolean
     * 
     * @throws MysqlConnectionException
     */
    public function isConnected(): bool
    {
        try {
            list($driver, $host, $port, $database, $username, $password) = $this->getConfig();
            new MysqlPdo("$driver:host=$host;port=$port;dbname=$database", $username, $password, null);
            return true;
        } catch (PDOException $e) {
            new MysqlConnectionException($e->getMessage());
            return false;
        }
    }

    /**
     * Make instance
     *
     * @return void
     * 
     * @throws MysqlConnectionException
     */
    public function makeInstance(): void
    {
        try {
            list($driver, $host, $port, $database, $username, $password) = $this->getConfig();
            $pdo = new MysqlPdo("$driver:host=$host;port=$port;dbname=$database", $username, $password, null);
            $pdo->exec("set names utf8");
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->instance = $pdo;
        } catch (PDOException $e) {
            throw new MysqlConnectionException($e->getMessage());
        }
    }
}
