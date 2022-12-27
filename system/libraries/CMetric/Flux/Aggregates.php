<?php

class CMetric_Flux_Aggregates {
    private $aggregates;

    public function mean($column = '_value') {
        $this->aggregates .= PHP_EOL
            . '|> mean(column: "' . $column . '") ';

        return $this;
    }

    public function min($column = '_value') {
        $this->aggregates .= PHP_EOL
            . '|> min(column: "' . $column . '") ';

        return $this;
    }

    public function max($column = '_value') {
        $this->aggregates .= PHP_EOL
            . '|> min(column: "' . $column . '") ';

        return $this;
    }

    public function sum($column = '_value') {
        $this->aggregates .= PHP_EOL
            . '|> sum(column: "' . $column . '") ';

        return $this;
    }

    public function mode($column = '_value') {
        $this->aggregates .= PHP_EOL
            . '|> mode(column: "' . $column . '") ';

        return $this;
    }

    public function spread($column = '_value') {
        $this->aggregates .= PHP_EOL
            . '|> spread(column: "' . $column . '") ';

        return $this;
    }

    /**
     * @param int $nRecords
     *
     * @return CMetric_Flux_Aggregates
     */
    public function movingAverage($nRecords) {
        $this->aggregates .= PHP_EOL
            . '|> movingAverage(n: "' . $nRecords . '") ';

        return $this;
    }

    /**
     * @param string $interval
     * @param string $duration
     * @param string $column
     *
     * @return CMetric_Flux_Aggregates
     */
    public function timedMovingAverage($interval, $duration, $column = '_value') {
        $this->aggregates .= PHP_EOL
            . '|> timedMovingAverage(every: ' . $interval . ', duration: "' . $duration . '", column: "' . $column . '") ';

        return $this;
    }

    /**
     * @param string $interval
     * @param string $aggregateMethod
     *
     * @return CMetric_Flux_Aggregates
     */
    public function aggregateWindow($interval, $aggregateMethod) {
        $this->aggregates .= PHP_EOL
            . '|> aggregateWindow(every: ' . $interval . ', fn: ' . $aggregateMethod . ') ';

        return $this;
    }

    public function getAggregates() {
        return $this->aggregates;
    }
}
