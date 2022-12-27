<?php
class CMetric_Flux_FluxFilter {
    protected $filterQuery;

    protected $selectQuery;

    protected $measurementName;

    protected $aggregates;

    protected $functions;

    public function __construct($select = null, $filter = null) {
        $this->selectQuery = $select;
        $this->selectQuery = $filter;
    }

    public function where($filters) {
        $this->filterQuery = 'and ' . $filters;

        return $this;
    }

    public function measurement($measurement) {
        $this->measurementName = $measurement;

        return $this;
    }

    public function select(Closure $filter) {
        $selectFilter = new CMetric_Flux_FluxSelect();
        $filter($selectFilter);
        $this->selectQuery = ' and (' . $selectFilter->getSelect() . ')';

        return $this;
    }

    public function getSelectQuery() {
        return $this->selectQuery;
    }

    public function getFilterQuery() {
        return $this->filterQuery;
    }

    public function getMeasurementName() {
        return $this->measurementName;
    }
}
