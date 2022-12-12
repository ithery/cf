<?php

/**
 * Class Null.
 */
class CMetric_Driver_NullDriver extends CMetric_DriverAbstract {
    /**
     * @return $this
     */
    public function flush() {
        return $this;
    }

    /**
     * @param CMetric_Metric $metric
     *
     * @return array
     */
    public function format(CMetric_Metric $metric) {
        return [];
    }

    /**
     * @param CMetric_QueryBuilder $query
     *
     * @return array
     */
    public function query(CMetric_QueryBuilder $query) {
        return [];
    }
}
