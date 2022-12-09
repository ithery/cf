<?php
/**
 * Class CMetric_Metric.
 *
 * @see CMetric
 */
class CMetric_QueryBuilder extends CMetric_QueryBuilderAbstract {
    use CMetric_Trait_HasDriverTrait;

    public function __construct($driver = null) {
        $this->driver = $driver;
        $this->period = CPeriod::hours(4);
    }

    public function get() {
        return $this->getDriver()->query($this);
    }
}
