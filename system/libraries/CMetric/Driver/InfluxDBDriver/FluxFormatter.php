<?php

class CMetric_Driver_InfluxDBDriver_FluxFormatter {
    /**
     * @var CMetric_QueryBuilder
     */
    protected $query;

    public function __construct(CMetric_QueryBuilder $query) {
        $this->query = $query;
    }

    public function toFluxString() {
        $fluxString = '';
        $fluxString .= $this->formatFrom($this->query->getFrom()) . PHP_EOL;
        $fluxString .= '  |> ' . $this->formatRange($this->query->getPeriod()) . PHP_EOL;
        $whereMeasurements = $this->query->getWhereMeasurements();
        if (count($whereMeasurements) > 0) {
            $fluxString .= '  |> ' . $this->formatWhereMeasurements($whereMeasurements) . PHP_EOL;
        }
        $whereFields = $this->query->getWhereFields();
        if (count($whereFields) > 0) {
            $fluxString .= '  |> ' . $this->formatWhereFields($whereFields) . PHP_EOL;
        }
        //$fluxString .= '  |> filter(fn:(r) => r._measurement == "inspector_transaction" and r._field == "value")' . PHP_EOL;
        $window = $this->query->getWindow();
        if ($window) {
            $fluxString .= '  |> ' . $this->formatWindow($window) . PHP_EOL;
        }

        $aggregateWindow = $this->query->getAggregateWindow();
        if ($aggregateWindow) {
            $fluxString .= '  |> ' . $this->formatAggregateWindow($aggregateWindow) . PHP_EOL;
        }

        $aggregate = $this->query->getAggregate();
        if ($aggregate) {
            $fluxString .= '  |> ' . $this->formatAggregate($aggregate) . PHP_EOL;
        }

        //$fluxString .= '  |> group(columns: ["_start"])' . PHP_EOL;
        //$fluxString .= '  |> yield(name: "count")' . PHP_EOL;

        return $fluxString;
    }

    private function formatAggregate($aggregate) {
        return $aggregate . '()';
    }

    private function formatAggregateWindow($aggregateWindow) {
        $statement = '';
        $every = carr::get($aggregateWindow, 'every');
        $fn = carr::get($aggregateWindow, 'fn');
        $column = carr::get($aggregateWindow, 'column');
        if ($every) {
            $statement .= ', every: ' . $every;
        }

        if ($fn) {
            $statement .= ', fn: ' . $fn;
        }
        if ($column) {
            $statement .= ', column: "' . $column . '"';
        }

        $statement = cstr::substr($statement, 2);

        //return 'filter(fn:(r) => r._measurement == "inspector_transaction")';
        return 'aggregateWindow(' . $statement . ')';
        //return 'filter(fn:(r) => r._field == "'.carr::get($where, 'column').'" and r._value == "'.carr::get($where, 'value').'")';
        //return 'filter(fn:(r) => r._measurement == "inspector_transaction" and r._field == "model" and r._value == "transaction")';
    }

    private function formatWindow($window) {
        $statement = '';
        $every = carr::get($window, 'every');
        if ($every) {
            $statement .= ', every: ' . $every;
        }

        $statement = cstr::substr($statement, 2);

        //return 'filter(fn:(r) => r._measurement == "inspector_transaction")';
        return 'window(' . $statement . ')';
        //return 'filter(fn:(r) => r._field == "'.carr::get($where, 'column').'" and r._value == "'.carr::get($where, 'value').'")';
        //return 'filter(fn:(r) => r._measurement == "inspector_transaction" and r._field == "model" and r._value == "transaction")';
    }

    private function formatWhereFields($fields) {
        $statement = '';
        foreach ($fields as $field) {
            $statement .= ' or r._field == "' . $field . '"';
        }

        $statement = cstr::substr($statement, 4);

        //return 'filter(fn:(r) => r._measurement == "inspector_transaction")';
        return 'filter(fn:(r) => ' . $statement . ')';
        //return 'filter(fn:(r) => r._field == "'.carr::get($where, 'column').'" and r._value == "'.carr::get($where, 'value').'")';
        //return 'filter(fn:(r) => r._measurement == "inspector_transaction" and r._field == "model" and r._value == "transaction")';
    }

    private function formatWhereMeasurements($measurements) {
        $statement = '';
        foreach ($measurements as $measurement) {
            $statement .= ' or r._measurement == "' . $measurement . '"';
        }

        $statement = cstr::substr($statement, 4);

        //return 'filter(fn:(r) => r._measurement == "inspector_transaction")';
        return 'filter(fn:(r) => ' . $statement . ')';
        //return 'filter(fn:(r) => r._field == "'.carr::get($where, 'column').'" and r._value == "'.carr::get($where, 'value').'")';
        //return 'filter(fn:(r) => r._measurement == "inspector_transaction" and r._field == "model" and r._value == "transaction")';
    }

    private function formatFrom($from) {
        return 'from(bucket: "' . $from . '")';
    }

    private function formatRange(CPeriod $period) {
        $start = $period->startDate->toIso8601String();
        $stop = $period->endDate->toIso8601String();

        return 'range(start: ' . $start . ', stop: ' . $stop . ')';
    }
}
