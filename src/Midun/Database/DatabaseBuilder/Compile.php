<?php

namespace Midun\Database\DatabaseBuilder;

class Compile
{
    /**
     * List of rows
     * 
     * @var array
     */
    public array $rows = [];

    /**
     * Execute building columns
     * 
     * @param array $columns
     * 
     * @return array
     */
    public function exec(array $columns): array
    {
        foreach ($columns as $column) {
            $column = (object) $column;
            if (isset($column->timestamps) && $column->timestamps == 1) {
                $this->rows[] = 'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
                $this->rows[] = 'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
                continue;
            }
            $row = '';
            if (isset($column->column)) {
                $row .= $column->column;
                $row .= ' ' . $column->dataType;
                $row .= (isset($column->length) && $column->length > 0) ? "($column->length)" : '';
                $row .= $column->autoIncrement == 1 && $column->pk != 1 ? ' UNSIGNED' : '';
                $row .= $column->unsigned == 1 ? ' UNSIGNED' : '';
                $row .= $column->nullable == 1 ? ' NULL' : ' NOT NULL';
                $row .= $column->default != '' ? " DEFAULT '$column->default'" : '';
                $row .= $column->pk == 1 ? ' PRIMARY KEY' : '';
                $row .= $column->autoIncrement == 1 ? ' AUTO_INCREMENT' : '';
                $row .= $column->comment != '' ? " COMMENT '$column->comment'" : '';
            }
            if(isset($column->foreignKey)) {
                $row .= "FOREIGN KEY ($column->foreignKey) REFERENCES $column->on($column->references)";
            }

            $this->rows[] = $row;
        }
        return $this->rows;
    }
}
