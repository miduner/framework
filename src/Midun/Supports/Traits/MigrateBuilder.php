<?php

namespace Midun\Supports\Traits;

use Midun\Database\DatabaseBuilder\Compile;
use Midun\Database\DatabaseBuilder\ColumnBuilder;

trait MigrateBuilder
{
    /**
     * Create table
     * 
     * @param string $table 
     * @param \Closure $closure
     * 
     * @return void
     */
    public function createMigrate(string $table, \Closure $closure): void
    {
        try {
            $columnBuilder = new ColumnBuilder;
            $closure($columnBuilder);
            $sqlBuildings = (new Compile)->exec($columnBuilder->columns());
            $createTableSql = "CREATE TABLE $table (";
            foreach ($sqlBuildings as $key => $sql) {
                if ($key == count($sqlBuildings) - 1) {
                    $createTableSql .= "$sql";
                } else {
                    $createTableSql .= "$sql, ";
                }
            }
            $createTableSql .= ');';

            app()->make('connection')->getConnection()->query($createTableSql);
        } catch (\PDOException $e) {
            app()->make(\Midun\Supports\ConsoleOutput::class)->printError($e->getMessage());
            exit(1);
        }
    }

    /**
     * Create if not exists table
     * 
     * @param string $table
     * @param \Closure $closure
     * 
     * @return void
     */
    public function createIfNotExistsMigrate(string $table, $closure): void
    {
        try {
            $columnBuilder = new ColumnBuilder;
            $closure($columnBuilder);
            $sqlBuildings = (new Compile)->exec($columnBuilder->columns());
            $createTableSql = "CREATE TABLE IF NOT EXISTS $table (";
            foreach ($sqlBuildings as $key => $sql) {
                if ($key == count($sqlBuildings) - 1) {
                    $createTableSql .= "$sql";
                } else {
                    $createTableSql .= "$sql, ";
                }
            }
            $createTableSql .= ');';
            app()->make('connection')->getConnection()->query($createTableSql);
        } catch (\PDOException $e) {
            app()->make(\Midun\Supports\ConsoleOutput::class)->printError($e->getMessage());
            exit(1);
        }
    }

    /**
     * Execute migrate table
     * 
     * @param string $table
     * 
     * @return void
     */
    public function dropMigrate(string $table): void
    {
        try {
            $dropTableSql = "DROP TABLE $table";
            app()->make('connection')->getConnection()->query($dropTableSql);
        } catch (\PDOException $e) {
            app()->make(\Midun\Supports\ConsoleOutput::class)->printError($e->getMessage());
            exit(1);
        }
    }

    /**
     * Execute drop if exists table
     * 
     * @param string $table
     * 
     * @return void
     */
    public function dropIfExistsMigrate(string $table): void
    {
        try {
            $dropTableSql = "DROP TABLE IF EXISTS $table";
            app()->make('connection')->getConnection()->query($dropTableSql);
        } catch (\PDOException $e) {
            app()->make(\Midun\Supports\ConsoleOutput::class)->printError($e->getMessage());
            exit(1);
        }
    }

    /**
     * Execute truncate table
     * 
     * @param string $table
     * 
     * @return void
     */
    public function truncateMigrate(string $table): void
    {
        try {
            $dropTableSql = "TRUNCATE $table";
            app()->make('connection')->getConnection()->query($dropTableSql);
        } catch (\PDOException $e) {
            app()->make(\Midun\Supports\ConsoleOutput::class)->printError($e->getMessage());
            exit(1);
        }
    }

    /**
     * Set table migration
     * 
     * @param string $table
     * 
     * @param ColumnBuilder $columns
     * 
     * @return void
     */
    public function tableMigrate(string $table, ColumnBuilder $columns): void
    {
        $this->table = $table;
        $this->columns = $columns;
    }
}
