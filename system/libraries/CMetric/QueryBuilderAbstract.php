<?php
/**
 * Class CMetric_QueryBuilderAbstract.
 *
 * @see CDatabase_Query_Builder
 */
abstract class CMetric_QueryBuilderAbstract {
    /**
     * The current query value bindings.
     *
     * @var array
     */
    protected $bindings = [
        'where' => [],
    ];

    /**
     * The bucket which the query is targeting.
     *
     * @var string
     */
    protected $from;

    /**
     * @var CPeriod
     */
    protected $period;

    /**
     * The where constraints for the measurement query.
     *
     * @var array
     */
    protected $whereMeasurements = [];

    /**
     * The where constraints for the fields query.
     *
     * @var array
     */
    protected $whereFields = [];

    /**
     * The window.
     *
     * @var null|array
     */
    protected $window = null;

    /**
     * The window.
     *
     * @var null|array
     */
    protected $aggregateWindow = null;

    /**
     * The aggregate.
     *
     * @var null|string
     */
    protected $aggregate = null;

    /**
     * @param string $from
     *
     * @return $this
     */
    public function setFrom($from) {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrom() {
        return $this->from;
    }

    public function setAggregate($aggregate) {
        $this->aggregate = $aggregate;

        return $this;
    }

    public function getAggregate() {
        return $this->aggregate;
    }

    public function setPeriod(CPeriod $period) {
        $this->period = $period;

        return $this;
    }

    public function getPeriod() {
        return $this->period;
    }

    public function getWhereFields() {
        return $this->whereFields;
    }

    public function getWhereMeasurements() {
        return $this->whereMeasurements;
    }

    public function setAggregateWindow($options) {
        $this->aggregateWindow = $options;

        return $this;
    }

    public function getAggregateWindow() {
        return $this->aggregateWindow;
    }

    public function setWindow($options) {
        $this->window = $options;

        return $this;
    }

    public function getWindow() {
        return $this->window;
    }

    public function getBindings() {
        return $this->bindings;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param string|array $measurement
     *
     * @return $this
     */
    public function whereMeasurement($measurement) {
        if (is_array($measurement)) {
            foreach ($measurement as $m) {
                $this->whereMeasurement($m);
            }
        } else {
            $this->whereMeasurements[] = $measurement;
        }

        return $this;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param string|array $field
     *
     * @return $this
     */
    public function whereField($field) {
        if (is_array($field)) {
            foreach ($field as $f) {
                $this->whereField($f);
            }
        } else {
            $this->whereFields[] = $field;
        }

        return $this;
    }
}
