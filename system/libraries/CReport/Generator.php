<?php

class CReport_Generator {
    /**
     * @var CReport_Builder_Report
     */
    protected $report;

    /**
     * @var CManager_Contract_DataProviderInterface
     */
    protected $dataProvider;

    public function __construct(CReport_Builder_Report $report, $dataProvider = null) {
        $this->report = $report;
        $this->dataProvider = $dataProvider;
    }

    protected function generate(CReport_Generator_ProcessorAbstract $processor) {
        $this->report->generate($processor);
    }

    public function getPdf() {
        // $this->report()->setProcessor($this->manager()->createPdfProcessor());
        $processor = new CReport_Generator_Processor_PdfProcessor($this->report);
        // $instructions = $this->generateInstructions
        // CReport_Jasper_Manager::instance()->getGenerator()->generateReport($this);
        $this->report->generate($processor);
        $pdf = $processor->getOutput();

        // $pdf = CReport_Jasper_Instructions::get();

        return $pdf;
    }
}
