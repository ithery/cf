<?php

use Illuminate\Contracts\Support\Arrayable;

class CElement_FormInput_QueryBuilder_FilterBuilder implements Arrayable {
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

    public function withFilter($callback) {
        c::tap($this->addFilter(), $callback);

        return $this;
    }

    public function toArray() {
        return c::collect($this->filters)->map(function ($filter) {
            return $filter instanceof Arrayable ? $filter->toArray() : $filter;
        })->toArray();
    }
}
