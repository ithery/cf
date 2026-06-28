<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @internal
 */
class CModel_ModelInfo implements ArrayAccess {
    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $database;

    /**
     * @var string
     */
    public $table;

    /**
     * @var null|string
     */
    public $policy;

    /**
     * @var CCollection
     */
    public $attributes;

    /**
     * @var CCollection
     */
    public $relations;

    /**
     * @var CCollection
     */
    public $events;

    /**
     * @var CCollection
     */
    public $observers;

    /**
     * @var string
     */
    public $collection;

    /**
     * @var string
     */
    public $builder;

    /**
     * @var null|string
     */
    public $resource;

    /**
     * @param string      $class
     * @param string      $database
     * @param string      $table
     * @param null|string $policy
     * @param CCollection $attributes
     * @param CCollection $relations
     * @param CCollection $events
     * @param CCollection $observers
     * @param string      $collection
     * @param string      $builder
     * @param null|string $resource
     */
    public function __construct(
        $class,
        $database,
        $table,
        $policy,
        $attributes,
        $relations,
        $events,
        $observers,
        $collection,
        $builder,
        $resource
    ) {
        $this->class = $class;
        $this->database = $database;
        $this->table = $table;
        $this->policy = $policy;
        $this->attributes = $attributes;
        $this->relations = $relations;
        $this->events = $events;
        $this->observers = $observers;
        $this->collection = $collection;
        $this->builder = $builder;
        $this->resource = $resource;
    }

    /**
     * Convert the model info to an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'class' => $this->class,
            'database' => $this->database,
            'table' => $this->table,
            'policy' => $this->policy,
            'attributes' => $this->attributes,
            'relations' => $this->relations,
            'events' => $this->events,
            'observers' => $this->observers,
            'collection' => $this->collection,
            'builder' => $this->builder,
            'resource' => $this->resource,
        ];
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset) {
        return property_exists($this, $offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset) {
        if (!property_exists($this, $offset)) {
            throw new InvalidArgumentException("Property {$offset} does not exist.");
        }

        return $this->{$offset};
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws LogicException
     */
    public function offsetSet($offset, $value) {
        throw new LogicException(self::class . ' may not be mutated using array access.');
    }

    /**
     * @param mixed $offset
     *
     * @throws LogicException
     */
    public function offsetUnset($offset) {
        throw new LogicException(self::class . ' may not be mutated using array access.');
    }
}
