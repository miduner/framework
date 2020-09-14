<?php

namespace Midun\Queues;

abstract class Queue
{
    /**
     * Number of tries
     * 
     * @var int
     */
    protected int $tries = 3;

    /**
     * Handle queue
     * 
     * @return void
     */
    abstract function handle(): void;

    /**
     * Get tries
     * 
     * @return int
     */
    public function getTries(): int
    {
        return $this->tries;
    }

    /**
     * Prepare the instance for serialization.
     *
     * @return array
     */
    public function getSerializeData(): array
    {
        $properties = (new \ReflectionClass($this))->getProperties();

        $data = [];

        foreach ($properties as $property) {
            $data[$property->getName()] = $this->getPropertyValue($property);
        }

        return $data;
    }

    /**
     * Get the property value for the given property.
     *
     * @param  \ReflectionProperty  $property
     * @return mixed
     */
    protected function getPropertyValue(\ReflectionProperty $property)
    {
        $property->setAccessible(true);

        return $property->getValue($this);
    }
}
