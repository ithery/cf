<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_Metric_RangedMetric extends CElement_Component {
    /**
     * The ranges available for the metric.
     *
     * @var array
     */
    protected $ranges = [];

    /**
     * The selected range key.
     *
     * @var null|string
     */
    protected $selectedRangeKey;

    public function setRanges(array $ranges) {
        $this->ranges = $ranges;

        return $this;
    }

    public function setSelectedRange($key) {
        $this->selectedRangeKey = $key;

        return $this;
    }
}
