<?php

class CReport_Jasper_Report_Generator {
    protected $report;

    protected $isProcessingPageFooter;

    public function __construct(CReport_Jasper_Report $report) {
        $this->report = $report;
    }

    /**
     * @return CReport_Jasper_Report
     */
    public function getReport() {
        return $this->report;
    }
}
