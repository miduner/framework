<?php

namespace Midun\Database\Connections\Mysql;

use \PDO;
use \PDOException;
use Midun\Database\Connections\Mysql\MysqlPdo;

class Connection
{
    /**
     * Driver database connection
     */
    private $driver;

    /**
     * Instance of connection
     */
    private $instance;

    public function __construct()
    {
        $this->setDriver(
            $this->getDefaultDriver()
        );
    }

    /**
     * Get list configuration from cache file
     */
    public function getConfig()
    {
        $driver = $this->getConnectInfo('driver');
        $host = $this->getConnectInfo('host');
        $port = $this->getConnectInfo('port');
        $database = $this->getConnectInfo('database');
        $username = $this->getConnectInfo('username');
        $password = $this->getConnectInfo('password');
        return [
            $driver,
            $host,
            $port,
            $database,
            $username,
            $password,
        ];
    }

    /**
     * Reset driver
     *
     * @return void
     */
    public function setDriver($driver)
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
     * @return boolean
     */
    public function isConnected()
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
     */
    private function makeInstance()
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

    /**
     * Get the connection
     *
     * @return \PDOInstance
     */
    public function getConnection()
    {
        if (!$this->instance) {
            $this->makeInstance();
        }
        return $this->instance;
    }

    /**
     * Get driver
     * 
     * @return string
     */
    protected function driver()
    {
        return $this->driver;
    }

    /**
     * Get default driver
     * 
     * @return string
     */
    protected function getDefaultDriver()
    {
        return config('database.default');
    }

    /**
     * Get connect driver
     * 
     * @param string $info
     * 
     * @return string
     */
    protected function getConnectInfo(string $info)
    {
        return config("database.connections.{$this->driver()}.{$info}");
    }
}
