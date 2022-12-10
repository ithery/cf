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
     * The where constraints for the query.
     *
     * @var array
     */
    protected $wheres = [];

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

    public function setPeriod(CPeriod $period) {
        $this->period = $period;

        return $this;
    }

    public function getPeriod() {
        return $this->period;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param string|array|\Closure $column
     * @param null|string           $operator
     * @param mixed                 $value
     * @param string                $boolean
     *
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and') {
        $type = 'Basic';

        // Now that we are working with just a simple query we can put the elements
        // in our array and add the query binding to our array of bindings that
        // will be bound to each SQL statements when it is finally executed.

        $this->wheres[] = compact(
            'type',
            'column',
            'operator',
            'value',
            'boolean'
        );

        if (!$value instanceof CDatabase_Query_Expression) {
            $this->addBinding($value, 'where');
        }

        return $this;
    }

    /**
     * Add a binding to the query.
     *
     * @param mixed  $value
     * @param string $type
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function addBinding($value, $type = 'where') {
        if (!array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }
}
