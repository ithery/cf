<?php

class CReport_Generator_Report {
    protected $report;

    public function __construct(CReport_Builder_Report $report) {
        $this->report = $report;
    }
}
