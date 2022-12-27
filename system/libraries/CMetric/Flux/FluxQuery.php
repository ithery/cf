<?php

class CMetric_Flux_FluxQuery {
    private $limitRecords;

    private $sortRecords;

    private $window;

    private $filter;

    private $aggregate;

    private $function;

    public function __construct() {
        $this->filter = new CMetric_Flux_FluxFilter();
        $this->aggregate = new CMetric_Flux_Aggregates();
        $this->function = new CMetric_Flux_Functions();
    }

    public function filter(Closure $filterAction) {
        $this->filter = new CMetric_Flux_FluxFilter();
        $filterAction($this->filter);

        return $this;
    }

    public function aggregates(Closure $filterAction) {
        $this->aggregate = new CMetric_Flux_Aggregates();
        $filterAction($this->aggregate);

        return $this;
    }

    public function window($interval, Closure $filterAction = null) {
        $this->window = 'window(every: ' . $interval . ')';
        if ($filterAction != null) {
            $this->aggregate = new CMetric_Flux_Aggregates();
            $filterAction($this->aggregate);
        }

        return $this;
    }

    public function functions($filterAction) {
        $this->function = new CMetric_Flux_Functions();
        $filterAction($this->function);

        return $this;
    }

    public function count() {
        $this->limitRecords = PHP_EOL
            . '|> count() ';

        return $this;
    }

    public function sort($desc = true, $columns = []) {
        $descStr = 'false';
        if ($desc) {
            $descStr = 'true';
        }
        $this->sortRecords = PHP_EOL
            . '|> sort(columns: [' . implode(', ', c::collect($columns)->map(function ($s) {
                return '"' . $s . '"';
            })->toArray()) . '], desc:' . $descStr . ') ';

        return $this;
    }

    public function limit($limit, $offset = 0) {
        $this->limitRecords = PHP_EOL
        . '|> limit(n:' . $limit . ', offset: ' . $offset . ') ';

        return $this;
    }

    public function toQuery() {
        $select = $this->filter->getSelectQuery();
        $filter = $this->filter->getFilterQuery();
        $measurement = $this->filter->getMeasurementName();
        $aggregates = $this->aggregate->getAggregates();
        $functions = $this->function->getFunctions();
        $filterQuery = '';
        $query = '';
        if ($measurement) {
            $filterQuery .= ' r._measurement == "' . $measurement . '"';
        }
        if ($select) {
            $filterQuery .= ' ' . $select;
        }
        if ($filter) {
            $filterQuery .= ' ' . $filter;
        }
        if ($filterQuery) {
            $query .= PHP_EOL
                . '|> filter(fn: (r) => ' . $filterQuery . ')';
        }
        if ($functions) {
            $query .= $functions;
            $query .= PHP_EOL;
        }
        if ($this->window) {
            $query .= $this->window;
            $query .= PHP_EOL;
        }

        if ($aggregates) {
            $query .= $aggregates;
            $query .= PHP_EOL;
        }
        if ($this->sortRecords) {
            $query .= $this->sortRecords;
            $query .= PHP_EOL;
        }

        if ($this->limitRecords) {
            $query .= $this->limitRecords;
            $query .= PHP_EOL;
        }

        return $query;
    }
}
