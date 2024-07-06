<?php

abstract class CReport_Jasper_ProcessorAbstract {
    protected $jasperReport;

    public function __construct(CReport_Jasper_Report $jasperReport) {
        $this->jasperReport = $jasperReport;
    }
}
