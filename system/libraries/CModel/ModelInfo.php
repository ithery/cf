<?php

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 *
 * @internal
 */
class CModel_ModelInfo implements Arrayable, ArrayAccess {
    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param class-string<TModel>                                                                                             $class      the model's fully-qualified class
     * @param string                                                                                                           $database   the database connection name
     * @param string                                                                                                           $table      the database table name
     * @param null|class-string                                                                                                $policy     the policy that applies to the model
     * @param \CCollection<int, array<string, mixed>>                                                                          $attributes the attributes available on the model
     * @param \CCollection<int, array{name: string, type: string, related: class-string<\Illuminate\Database\Eloquent\Model>}> $relations  the relations defined on the model
     * @param \CCollection<int, array{event: string, class: string}>                                                           $events     the events that the model dispatches
     * @param \CCollection<int, array{event: string, observer: array<int, string>}>                                            $observers  the observers registered for the model
     * @param class-string<\CModel_Collection<TModel>>                                                                         $collection the Collection class that collects the models
     * @param class-string<\CModel_Query<TModel>>                                                                              $builder    the Builder class registered for the model
     * @param null|\Illuminate\Http\Resources\Json\JsonResource                                                                $resource   the JSON resource that represents the model
     */
    public function __construct(
        public $class,
        public $database,
        public $table,
        public $policy,
        public $attributes,
        public $relations,
        public $events,
        public $observers,
        public $collection,
        public $builder,
        public $resource
    ) {
    }

    /**
     * Convert the model info to an array.
     *
     * @return array{
     *     "class": class-string<\Illuminate\Database\Eloquent\Model>,
     *     database: string,
     *     table: string,
     *     policy: null|class-string,
     *     attributes: \CCollection<int, array<string, mixed>>,
     *     relations: \CCollection<int, array{name: string, type: string, related: class-string<\Illuminate\Database\Eloquent\Model>}>,
     *     events: \CCollection<int, array{event: string, class: string}>,
     *     observers: \CCollection<int, array{event: string, observer: array<int, string>}>, collection: class-string<\Illuminate\Database\Eloquent\Collection<\Illuminate\Database\Eloquent\Model>>,
     *     builder: class-string<\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>,
     *     resource: null|\Illuminate\Http\Resources\Json\JsonResource
     * }
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
     * Determine if the given offset exists.
     */
    public function offsetExists(mixed $offset): bool {
        return property_exists($this, $offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @throws \InvalidArgumentException
     */
    public function offsetGet(mixed $offset): mixed {
        return property_exists($this, $offset) ? $this->{$offset} : throw new InvalidArgumentException("Property {$offset} does not exist.");
    }

    /**
     * Set the value at the given offset.
     *
     * @throws \LogicException
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        throw new LogicException(self::class . ' may not be mutated using array access.');
    }

    /**
     * Unset the value at the given offset.
     *
     * @throws \LogicException
     */
    public function offsetUnset(mixed $offset): void {
        throw new LogicException(self::class . ' may not be mutated using array access.');
    }
}
