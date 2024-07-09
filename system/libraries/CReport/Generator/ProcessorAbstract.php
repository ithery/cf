<?php

abstract class CReport_Generator_ProcessorAbstract {
    protected $report;

    public function __construct(CReport_Builder_Report $report) {
        $this->report = $report;
    }

    /**
     * @return mixed
     */
    abstract public function getOutput();
}
