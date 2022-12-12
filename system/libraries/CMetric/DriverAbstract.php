<?php

/**
 * Class CMetric_DriverAbstract.
 */
abstract class CMetric_DriverAbstract {
    /**
     * @var array
     */
    protected $metrics = [];

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var array
     */
    protected $extra = [];

    /**
     * @param $name
     * @param $value
     *
     * @return CMetric_Metric
     */
    public function create($name, $value = null) {
        $metric = new CMetric_Metric($name, $value, $this);
        $this->add($metric);

        return $metric;
    }

    /**
     * @return CMetric_QueryBuilder
     */
    public function createQuery() {
        $query = new CMetric_QueryBuilder($this);

        return $query;
    }

    /**
     * @param CMetric_Metric $metric
     *
     * @return $this
     */
    public function add(CMetric_Metric $metric) {
        $metric->setDriver($this);

        if ($metric->getTimestamp() == null) {
            $metric->setTimestamp(new \DateTime());
        }

        $this->metrics[] = $metric;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetrics() {
        return $this->metrics;
    }

    /**
     * Set default tags to be merged in on all metrics.
     *
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags) {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Set default extra to be merged in on all metrics.
     *
     * @param array $extra
     *
     * @return $this
     */
    public function setExtra(array $extra) {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @param CMetric_Metric $metric
     *
     * @return mixed
     */
    abstract public function format(CMetric_Metric $metric);

    /**
     * @param CMetric_QueryBuilder $metric
     *
     * @return mixed
     */
    abstract public function query(CMetric_QueryBuilder $query);

    /**
     * @return $this
     */
    abstract public function flush();
}
