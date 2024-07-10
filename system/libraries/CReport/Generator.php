<?php

class CReport_Generator {
    /**
     * @var CReport_Builder_Report
     */
    protected $report;

    /**
     * @var CReport_Builder_Dictionary
     */
    protected $dictionary;

    /**
     * @var CManager_Contract_DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var CReport_Generator_Evaluator
     */
    protected $evaluator;

    /**
     * @var CReport_Builder_Data
     */
    protected $data;

    /**
     * @var CReport_Builder_Row
     */
    protected $currentRow;

    public function __construct(CReport_Builder_Report $report, CReport_Builder_Dictionary $dictionary, CManager_Contract_DataProviderInterface $dataProvider = null) {
        $this->report = $report;
        $this->dictionary = $dictionary;
        $this->dataProvider = $dataProvider;

        $this->data = new CReport_Builder_Data($this->dataProvider->toEnumerable());
        $this->currentRow = carr::first($this->data);
        $this->evaluator = new CReport_Generator_Evaluator($this);
    }

    public function getFieldValue($field, $default = null) {
        return carr::get($this->currentRow, $field, $default);
    }

    public function getExpression(string $expression) {
        return $this->evaluator->getExpression($expression);
    }

    /**
     * @return CReport_Builder_Dictionary
     */
    public function getDictionary() {
        return $this->dictionary;
    }

    /**
     * @return CReport_Builder_Data
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return CReport_Builder_Row
     */
    public function getCurrentRow() {
        return $this->currentRow;
    }

    /**
     * @return $this
     */
    public function setCurrentRow(CReport_Builder_Row $row) {
        $this->currentRow = $row;

        return $this;
    }

    protected function generate(CReport_Generator_ProcessorAbstract $processor) {
        $this->report->generate($this, $processor);
    }

    public function getPdf() {
        // $this->report()->setProcessor($this->manager()->createPdfProcessor());
        $processor = new CReport_Generator_Processor_PdfProcessor($this->report);
        // $instructions = $this->generateInstructions
        // CReport_Jasper_Manager::instance()->getGenerator()->generateReport($this);
        $this->report->generate($this, $processor);
        $pdf = $processor->getOutput();

        // $pdf = CReport_Jasper_Instructions::get();

        return $pdf;
    }
}
