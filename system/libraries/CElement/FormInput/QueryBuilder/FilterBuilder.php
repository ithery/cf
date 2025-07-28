<?php

use Illuminate\Contracts\Support\Arrayable;

class CElement_FormInput_QueryBuilder_FilterBuilder implements Arrayable {
    /**
     * @var array
     */
    protected $filters;

    public function __construct() {
        $this->filters = [];
    }

    /**
     * @param null|string $id
     *
     * @return CElement_FormInput_QueryBuilder_Filter
     */
    public function addFilter($id = null) {
        $filter = new CElement_FormInput_QueryBuilder_Filter($id);

        $this->filters[] = $filter;

        return $filter;
    }

    /**
     * Add a filter to the builder and register a callback to manipulate it.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function withFilter($callback) {
        c::tap($this->addFilter(), $callback);

        return $this;
    }

    /**
     * Convert the filter builder's filters to an array.
     *
     * Iterates over each filter, converting it to an array if it implements
     * the Arrayable interface, otherwise includes the filter as is.
     *
     * @return array
     */
    public function toArray() {
        return c::collect($this->filters)->map(function ($filter) {
            return $filter instanceof Arrayable ? $filter->toArray() : $filter;
        })->toArray();
    }
}
