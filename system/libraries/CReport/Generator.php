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
     * @var CReport_Generator_Calculator
     */
    protected $calculator;

    /**
     * @var CReport_Generator_Formatter
     */
    protected $formatter;

    /**
     * @var CReport_Builder_Data
     */
    protected $data;

    /**
     * @var CReport_Builder_Row
     */
    protected $currentRow;

    /**
     * @var int
     */
    protected $pageNumber;

    /**
     * @var int
     */
    protected $reportCount;

    /**
     * @var int
     */
    protected $columnNumber;

    /**
     * @var bool
     */
    protected $isProcessingPageFooter;

    /**
     * @var bool
     */
    protected $isProcessingDetail;

    public function __construct(CReport_Builder_Report $report, CReport_Builder_Dictionary $dictionary, CManager_Contract_DataProviderInterface $dataProvider = null) {
        $this->report = $report;
        $this->dictionary = $dictionary;
        $this->dataProvider = $dataProvider;
        $this->data = $this->dataProvider ? new CReport_Builder_Data($this->dataProvider->toEnumerable()) : new CReport_Builder_Data(c::collect());
        $this->currentRow = carr::first($this->data);

        $this->evaluator = new CReport_Generator_Evaluator($this);
        $this->calculator = new CReport_Generator_Calculator($this);
        $this->formatter = new CReport_Generator_Formatter();
        $this->isProcessingPageFooter = false;
        $this->isProcessingDetail = false;
    }

    public function setProcessingPageFooter($bool) {
        $this->isProcessingPageFooter = $bool;

        return $this;
    }

    public function isProcessingPageFooter() {
        return $this->isProcessingPageFooter;
    }

    public function setProcessingDetail($bool) {
        $this->isProcessingDetail = $bool;

        return $this;
    }

    public function isProcessingDetail() {
        return $this->isProcessingDetail;
    }

    public function setDataProvider(CManager_Contract_DataProviderInterface $dataProvider) {
        $this->dataProvider = $dataProvider;
        $this->data = $this->dataProvider ? new CReport_Builder_Data($this->dataProvider->toEnumerable()) : new CReport_Builder_Data(c::collect());
        $this->currentRow = carr::first($this->data);
    }

    public function getFieldValue($field, $default = null) {
        $fields = explode('.', $field);
        $value = $this->currentRow;
        foreach ($fields as $field) {
            if ($value instanceof CModel) {
                $value = $value->$field;
            } else {
                $value = carr::get($value, $field);
            }
        }

        return $value;
    }

    public function getExpression(string $expression) {
        return $this->evaluator->getExpression($expression);
    }

    public function formatPattern($text, string $pattern) {
        return $this->formatter->formatPattern($text, $pattern);
    }

    /**
     * @return CReport_Builder_Dictionary
     */
    public function getDictionary() {
        return $this->dictionary;
    }

    /**
     * @return CReport_Builder_Report
     */
    public function getReport() {
        return $this->report;
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

    public function incrementPageNumber() {
        $this->pageNumber++;

        return $this;
    }

    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function getTotalRows() {
        return $this->data->count();
    }

    public function getColumnNumber() {
        return $this->columnNumber;
    }

    public function evaluatePrintWhenExpression(string $expression = null) {
        return $this->evaluator->evaluatePrintWhenExpression($expression, $this->currentRow);
    }

    /**
     * @return CCollection|CReport_Builder_Element_Groups[]
     */
    public function getGroups() {
        return $this->report->getGroupElements();
    }

    /**
     * @return null|CReport_Builder_Element_PageHeader
     */
    public function getPageHeader() {
        return $this->report->getPageHeaderElement();
    }

    /**
     * @return null|CReport_Builder_Element_PageFooter
     */
    public function getPageFooter() {
        return $this->report->getPageFooterElement();
    }

    /**
     * @return null|CReport_Builder_Element_ColumnHeader
     */
    public function getColumnHeader() {
        return $this->report->getColumnHeaderElement();
    }

    public function setColumnNumber($columnNumber) {
        $this->columnNumber = $columnNumber;

        return $this;
    }

    public function getReportCount() {
        return $this->reportCount;
    }

    public function setReportCount($reportCount) {
        $this->reportCount = $reportCount;

        return $this;
    }

    public function variablesCalculation() {
        return $this->calculator->variablesCalculation($this->currentRow);
    }

    public function getParameterValue($name, $default = null) {
        return $this->dictionary->getParameterValue($name, $default);
    }

    public function getVariableValue($name, $default = null) {
        //get the global variables
        $globalVariables = [
            'REPORT_COUNT' => $this->getReportCount(),
            'COLUMN_NUMBER' => $this->getColumnNumber(),
            'PAGE_NUMBER' => $this->getPageNumber(),
            'totalRows' => $this->getTotalRows(),
        ];
        if (array_key_exists($name, $globalVariables)) {
            return $globalVariables[$name];
        }

        return $this->dictionary->getVariableValue($name, $default);
    }

    protected function generate(CReport_Generator_ProcessorAbstract $processor) {
        $this->pageNumber = 1;
        $this->dictionary->fillVariables($this->report->getVariableElements(), $this);
        $this->report->generate($this, $processor);
    }

    public function getPdf() {
        $processor = new CReport_Generator_Processor_PdfProcessor($this->report);
        $this->generate($processor);
        $pdf = $processor->getOutput();

        // $pdf = CReport_Jasper_Instructions::get();

        return $pdf;
    }
}
