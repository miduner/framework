<?php

namespace Midun\Database\DatabaseBuilder;

class ColumnBuilder
{
    /**
     * List of columns
     * 
     * @var array
     */
    public array $columns = [];

    /**
     * Name of current column
     * 
     * @var string
     */
    public string $name = "";

    /**
     * Length of current column
     * 
     * @var int
     */
    public int $length;

    /**
     * Data type of current column
     * 
     * @var string
     */
    public string $dataType;

    /**
     * Flag nullable of current column
     * 
     * @var bool
     */
    public bool $nullable = false;

    /**
     * Flag unsigned of current column
     * 
     * @var bool
     */
    public bool $unsigned = false;

    /**
     * Flag primary key of current column
     * 
     * @var bool
     */
    public bool $pk = false;

    /**
     * Flag is timestamps
     * 
     * @var bool
     */
    public bool $timestamps = false;

    /**
     * Flag auto increments of current column
     * 
     * @var bool
     */
    public bool $autoIncrement = false;

    /**
     * Default value of current column
     * 
     * @var string
     */
    public string $default = "";

    /**
     * Comment of current column
     * 
     * @var string
     */
    public string $comment = "";

    /**
     * Flag uni of current column
     * 
     * @var bool
     */
    public bool $unique = false;

    /**
     * Set column foreign
     * 
     * @var string
     */
    public string $foreignKey = "";

    /**
     * Set column references
     * 
     * @var string
     */
    public string $references = "";

    /**
     * Set table references
     * 
     * @var string
     */
    public string $on = "";

    /**
     * Flag on update
     * 
     * @var bool
     */
    public bool $onUpdate;

    /**
     * Flag on delete
     * 
     * @var bool
     */
    public bool $onDelete;

    /**
     * Reset all property
     *
     * @return void
     */
    public function reset(): void
    {
        $this->name = "";
        $this->length = 0;
        $this->dataType = "";
        $this->nullable = false;
        $this->pk = false;
        $this->timestamps = false;
        $this->unsigned = false;
        $this->autoIncrement = false;
        $this->default = "";
        $this->comment = "";
        $this->unique = false;
        $this->foreignKey = "";
        $this->references = "";
        $this->on = "";
        $this->onUpdate = false;
        $this->onDelete = false;
    }

    /**
     * Add new column compile
     *
     * @return void
     */
    public function addColumn(): void
    {
        if (!empty($this->name) || $this->timestamps) {
            $this->columns[] = [
                'column' => $this->name,
                'length' => $this->length,
                'dataType' => $this->dataType,
                'nullable' => $this->nullable,
                'unsigned' => $this->unsigned,
                'pk' => $this->pk,
                'timestamps' => $this->timestamps,
                'autoIncrement' => $this->autoIncrement,
                'default' => $this->default,
                'comment' => $this->comment,
                'unique' => $this->unique,
            ];
            $this->reset();
        }
        if ($this->foreignKey) {
            $this->columns[] = [
                'foreignKey' => $this->foreignKey,
                'references' => $this->references,
                'on' => $this->on,
                'onUpdate' => $this->onUpdate,
                'onDelete' => $this->onDelete,
            ];
            $this->reset();
        }
    }

    /**
     * Set column foreign key
     * 
     * @param string $foreignKey
     *
     * @return ColumnBuilder
     */
    public function foreign(string $foreignKey): ColumnBuilder
    {
        $this->addColumn();
        $this->foreignKey = $foreignKey;
        return $this;
    }

    /**
     * Set column references
     * 
     * @param string $references
     *
     * @return ColumnBuilder
     */
    public function references(string $references): ColumnBuilder
    {
        $this->references = $references;
        return $this;
    }

    /**
     * Set table reference
     * 
     * @param string $table
     *
     * @return ColumnBuilder
     */
    public function on(string $table): ColumnBuilder
    {
        $this->on = $table;
        return $this;
    }

    /**
     * Set type on update
     * 
     * @param string $onUpdate
     *
     * @return ColumnBuilder
     */
    public function onUpdate(string $onUpdate): ColumnBuilder
    {
        $this->onUpdate = $onUpdate;
        return $this;
    }

    /**
     * Set type on delete
     * 
     * @param string $onDelete
     *
     * @return ColumnBuilder
     */
    public function onDelete(string $onDelete): ColumnBuilder
    {
        $this->onDelete = $onDelete;
        return $this;
    }

    /**
     * Set boolean
     * 
     * @param string $boolean
     *
     * @return ColumnBuilder
     */
    public function boolean(string $column): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->dataType = 'BOOLEAN';
        return $this;
    }

    /**
     * Set bit
     * 
     * @param string $bit
     *
     * @return ColumnBuilder
     */
    public function bit(string $column): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->dataType = 'BIT';
        return $this;
    }

    /**
     * Set big integer
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function bigInteger(string $column, int $length = 20): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'BIGINT';
        return $this;
    }

    /**
     * Set small integer
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function smallInteger(string $column, int $length = 20): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'SMALLINT';
        return $this;
    }

    /**
     * Set medium integer
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function mediumInteger(string $column, int $length = 20): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'MEDIUMINT';
        return $this;
    }

    /**
     * Set decimal
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function decimal(string $column, int $length = 20): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'DECIMAL';
        return $this;
    }

    /**
     * Set date
     * 
     * @param string $column
     *
     * @return ColumnBuilder
     */
    public function date(string $column): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->dataType = 'DATE';
        return $this;
    }

    /**
     * Set date time
     * 
     * @param string $column
     *
     * @return ColumnBuilder
     */
    public function dateTime(string $column): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->dataType = 'DATETIME';
        return $this;
    }

    /**
     * Set timestamp
     * 
     * @param string $column
     *
     * @return ColumnBuilder
     */
    public function timestamp(string $column): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->dataType = 'TIMESTAMP';
        return $this;
    }

    /**
     * Set time
     * @param string $column
     *
     * @return ColumnBuilder
     */
    public function time(string $column): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->dataType = 'TIME';
        return $this;
    }

    /**
     * Set year
     * 
     * @param string $column
     *
     * @return ColumnBuilder
     */
    public function year(string $column): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->dataType = 'YEAR';
        return $this;
    }

    /**
     * Set integer
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function integer(string $column, int $length = 11): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'INT';
        return $this;
    }

    /**
     * Set tiny integer
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function tinyInteger(string $column, int $length = 2): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'TINYINT';
        return $this;
    }

    /**
     * Set timestamps
     *
     * @return ColumnBuilder
     */
    public function timestamps(): ColumnBuilder
    {
        $this->addColumn();
        $this->timestamps = true;
        return $this;
    }

    /**
     * Set big increments
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function bigIncrements(string $column = 'id', int $length = 20): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'BIGINT';
        $this->pk = true;
        $this->autoIncrement = true;
        return $this;
    }

    /**
     * Set increments
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function increments(string $column = 'id', int $length = 11): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'INT';
        $this->pk = true;
        $this->autoIncrement = true;
        return $this;
    }

    /**
     * Set string
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function string(string $column, int $length = 255): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'VARCHAR';
        return $this;
    }

    /**
     * Set text
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function text(string $column, int $length = 1000): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'TEXT';
        return $this;
    }

    /**
     * Set long text
     * 
     * @param string $column
     * @param integer $length
     *
     * @return ColumnBuilder
     */
    public function longText(string $column, int $length = 0): ColumnBuilder
    {
        $this->addColumn();
        $this->name = $column;
        $this->length = $length;
        $this->dataType = 'LONGTEXT';
        return $this;
    }

    /**
     * Set unsigned
     *
     * @return ColumnBuilder
     */
    public function unsigned(): ColumnBuilder
    {
        $this->unsigned = true;
        return $this;
    }

    /**
     * Set default
     * 
     * @param string $value
     *
     * @return ColumnBuilder
     */
    function default(string $value): ColumnBuilder
    {
        $this->default = $value;
        return $this;
    }

    /**
     * Set comment
     * @param string $value
     *
     * @return ColumnBuilder
     */
    public function comment(string $value): ColumnBuilder
    {
        $this->comment = $value;
        return $this;
    }

    /**
     * Set nullable
     *
     * @return ColumnBuilder
     */
    public function nullable(): ColumnBuilder
    {
        $this->nullable = true;
        return $this;
    }

    /**
     * Set unique
     *
     * @return ColumnBuilder
     */
    public function unique(): ColumnBuilder
    {
        $this->unique = true;
        return $this;
    }

    /**
     * Set done
     *
     * @return ColumnBuilder
     */
    public function done(): ColumnBuilder
    {
        $this->addColumn();
        return $this;
    }

    /**
     * Get list columns added
     *
     * @return array
     */
    public function columns(): array
    {
        $this->addColumn();
        return $this->columns;
    }

    /**
     * Set last column
     */
    public function __destruct()
    {
        $this->addColumn();
    }
}
