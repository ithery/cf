<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_Metric_TrendMetric extends CElement_Component_Metric_ValueMetric {
    /**
     * Trend metric unit constants.
     */
    const BY_MONTHS = 'month';

    const BY_WEEKS = 'week';

    const BY_DAYS = 'day';

    const BY_HOURS = 'hour';

    const BY_MINUTES = 'minute';

    public function __construct($id = null) {
        parent::__construct($id);
        $this->view = 'cresenity/element/component/metric/trend';
    }

    protected function build() {
        parent::build();
    }
}
