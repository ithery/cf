<?php

abstract class CMetric_QueryBuilderAbstract {
    protected $from;

    protected $filters;

    protected $aggregate;

    protected $yield;

    protected $range;

    abstract public function toString();

    public function setFrom($from) {
        $this->from = $from;

        return $this;
    }

    public function addFilter($filter) {
        $this->filters = $filter;

        return $this;
    }

    public function __toString() {
        return $this->toString();
    }
}
