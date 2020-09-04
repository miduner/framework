<?php

namespace Midun\Eloquent;

class Collection extends \ArrayObject
{
    public function __construct(array $input = [], int $flag = \ArrayObject::STD_PROP_LIST)
    {
        parent::__construct($input, $flag);
    }

    public function toArray()
    {
        $array = objectToArray($this);

        return $array;
    }
}
