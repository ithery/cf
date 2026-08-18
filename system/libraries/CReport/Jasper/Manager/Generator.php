<?php
/**
 * @internal
 */
class CReport_Jasper_Manager_Generator {
    protected $report;

    protected $isProcessingPageFooter;

    public function __construct() {
        $this->report = null;
    }

    public function generateReport(CReport_Jasper_Report $report) {
        $this->report = $report;

        $this->report->getRoot()->generate($this->report);
        // cdbg::dd($this->report->getInstructions()->all());
        CReport_Jasper_Instructions::runInstructions();
        $this->report = null;
    }

    public function isGenerating() {
        return $this->report != null;
    }

    /**
     * @return CReport_Jasper_Report
     */
    public function getReport() {
        return $this->report;
    }
}
