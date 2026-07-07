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

    /**
     * @var bool
     */
    protected $isProcessingHook;

    /**
     * @var CReport_Generator_Instruction[]
     */
    protected $instructions;

    /**
     * @var bool
     */
    private $columnFooterDrawn;

    /**
     * @var bool
     */
    private $isProcessingPdf;

    /**
     * @var bool
     */
    private $isProcessingExcel;

    /**
     * @var null|CReport_Generator_ProcessorAbstract
     */
    private $processor;

    /**
     * @var null|CReport_Builder_ElementAbstract
     */
    private $currentBand;

    /**
     * @var int
     */
    private $detailNumberOnPage;

    /**
     * @param CReport_Builder_Report                        $report
     * @param CReport_Builder_Dictionary                    $dictionary
     * @param null|CManager_Contract_DataProviderInterface $dataProvider
     */
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
        $this->instructions = [];
        $this->columnFooterDrawn = false;
        $this->isProcessingPdf = false;
        $this->isProcessingExcel = false;
        $this->processor = null;
        $this->currentBand = null;
        $this->detailNumberOnPage = 0;
    }

    /**
     * @param CReport_Builder_ElementAbstract $currentBand
     *
     * @return void
     */
    public function setCurrentBand(CReport_Builder_ElementAbstract $currentBand) {
        $this->currentBand = $currentBand;
    }

    /**
     * @return null|CReport_Builder_ElementAbstract
     */
    public function getCurrentBand() {
        return $this->currentBand;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setProcessingPageFooter($bool) {
        $this->isProcessingPageFooter = $bool;

        return $this;
    }

    /**
     * Queue an instruction to run after the whole report is generated, e.g. for $V{PAGE_COUNT}.
     *
     * @param CReport_Generator_ProcessorAbstract $processor
     * @param Closure                              $closure
     *
     * @return void
     */
    public function addInstruction(CReport_Generator_ProcessorAbstract $processor, Closure $closure) {
        $this->instructions[] = new CReport_Generator_Instruction($processor->getY(), $this->getPageNumber(), $closure);
    }

    /**
     * @return bool
     */
    public function isProcessingHook() {
        return $this->isProcessingHook;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setProcessingHook($bool) {
        $this->isProcessingHook = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessingPageFooter() {
        return $this->isProcessingPageFooter;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setProcessingDetail($bool) {
        $this->isProcessingDetail = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessingDetail() {
        return $this->isProcessingDetail;
    }

    /**
     * @param CManager_Contract_DataProviderInterface $dataProvider
     *
     * @return void
     */
    public function setDataProvider(CManager_Contract_DataProviderInterface $dataProvider) {
        $this->dataProvider = $dataProvider;
        $this->data = $this->dataProvider ? new CReport_Builder_Data($this->dataProvider->toEnumerable()) : new CReport_Builder_Data(c::collect());
        $this->currentRow = carr::first($this->data);
    }

    /**
     * Get a field value from the current row, with dot notation for nested relations.
     *
     * @param string     $field
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getFieldValue($field, $default = null) {
        $fields = explode('.', $field);
        $value = $this->currentRow;
        foreach ($fields as $fieldPart) {
            if ($value instanceof CModel) {
                $value = $value->$fieldPart;
            } else {
                $value = carr::get($value, $fieldPart);
            }
        }

        return $value !== null ? $value : $default;
    }

    /**
     * @param string $expression
     * @param string $evaluationTime
     *
     * @return mixed
     */
    public function getExpression(string $expression, string $evaluationTime = CReport::EVALUATION_TIME_NOW) {
        return $this->evaluator->getExpression($expression, $evaluationTime);
    }

    /**
     * @param mixed  $text
     * @param string $pattern
     *
     * @return mixed
     */
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
     * @return null|CReport_Builder_Row
     */
    public function getCurrentRow() {
        return $this->currentRow;
    }

    /**
     * @return null|CReport_Builder_Row
     */
    public function getNextRow() {
        $nextIndex = $this->reportCount + 1;
        if ($this->data && $this->data->count() > $nextIndex) {
            return $this->data[$nextIndex];
        }

        return null;
    }

    /**
     * @return $this
     */
    public function setCurrentRow(CReport_Builder_Row $row) {
        $this->currentRow = $row;

        return $this;
    }

    /**
     * @return $this
     */
    public function incrementPageNumber() {
        $this->pageNumber++;
        $this->columnFooterDrawn = false;
        $this->detailNumberOnPage = 0;

        return $this;
    }

    /**
     * @return void
     */
    public function incrementDetailNumberOnPage() {
        $this->detailNumberOnPage = $this->detailNumberOnPage + 1;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setColumnFooterDrawn($bool = true) {
        $this->columnFooterDrawn = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isColumnFooterDrawn() {
        return $this->columnFooterDrawn;
    }

    /**
     * @return int
     */
    public function getPageNumber() {
        return $this->pageNumber;
    }

    /**
     * @return int
     */
    public function getTotalRows() {
        return $this->data->count();
    }

    /**
     * @return int
     */
    public function getColumnNumber() {
        return $this->columnNumber;
    }

    /**
     * @param null|string $expression
     *
     * @return bool
     */
    public function evaluatePrintWhenExpression(string $expression = null) {
        return $this->evaluator->evaluatePrintWhenExpression($expression);
    }

    /**
     * @return int
     */
    public function getDetailNumberOnPage() {
        return $this->detailNumberOnPage;
    }

    /**
     * @return CCollection|CReport_Builder_Element_Group[]
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
     * @return null|CReport_Builder_Element_ColumnFooter
     */
    public function getColumnFooter() {
        return $this->report->getColumnFooterElement();
    }

    /**
     * @return CReport_Builder_Element_Font[]
     */
    public function getFonts() {
        return $this->report->getFontElements();
    }

    /**
     * @return null|CReport_Builder_Element_Style
     */
    public function getStyle(string $styleName) {
        return $this->report->getStyleElements()->filter(function (CReport_Builder_Element_Style $style) use ($styleName) {
            return $style->getName() == $styleName;
        })->first();
    }

    /**
     * @return null|CReport_Builder_Element_ColumnHeader
     */
    public function getColumnHeader() {
        return $this->report->getColumnHeaderElement();
    }

    /**
     * @param int $columnNumber
     *
     * @return $this
     */
    public function setColumnNumber($columnNumber) {
        $this->columnNumber = $columnNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getReportCount() {
        return $this->reportCount;
    }

    /**
     * @param int $reportCount
     *
     * @return $this
     */
    public function setReportCount($reportCount) {
        $this->reportCount = $reportCount;

        return $this;
    }

    /**
     * Recalculate all variables against the current row.
     *
     * @return void
     */
    public function variablesCalculation() {
        $this->calculator->variablesCalculation();
    }

    /**
     * @param string     $name
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getParameterValue($name, $default = null) {
        return $this->dictionary->getParameterValue($name, $default);
    }

    /**
     * @param string     $name
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getVariableValue($name, $default = null) {
        //get the global variables
        $globalVariables = [
            'REPORT_COUNT' => $this->getReportCount(),
            'COLUMN_NUMBER' => $this->getColumnNumber(),
            'PAGE_NUMBER' => $this->getPageNumber(),
            'PAGE_COUNT' => $this->getPageNumber(),
            'totalRows' => $this->getTotalRows(),
        ];
        if (array_key_exists($name, $globalVariables)) {
            return $globalVariables[$name];
        }

        return $this->dictionary->getVariableValue($name, $default);
    }

    /**
     * @return bool
     */
    public function isProcessingPdf() {
        return $this->isProcessingPdf;
    }

    /**
     * @return bool
     */
    public function isProcessingExcel() {
        return $this->isProcessingExcel;
    }

    /**
     * @return null|CReport_Generator_ProcessorAbstract
     */
    public function getProcessor() {
        return $this->processor;
    }

    /**
     * Check whether rendering the current and next detail row would trigger a page break.
     *
     * @return bool
     */
    public function willChangePage() {
        if ($this->isProcessingDetail() && $this->isProcessingPdf()) {
            $processor = $this->getProcessor();
            /** @var CReport_Generator_Processor_PdfProcessor $processor */
            $detail = $this->getCurrentBand();
            /** @var CReport_Builder_Element_Detail $detail */
            $nextRow = $this->getNextRow();
            if ($nextRow) {
                $currentHeight = $detail->getHeightForOverflow($this, $processor);
                $currentRow = $this->getCurrentRow();
                $this->setCurrentRow($nextRow);
                $nextHeight = $detail->getHeightForOverflow($this, $processor);
                $this->setCurrentRow($currentRow);
                if ($processor->willChangePage($this, $currentHeight + $nextHeight)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Run the report generation against a processor.
     *
     * @param CReport_Generator_ProcessorAbstract $processor
     *
     * @return void
     */
    protected function generate(CReport_Generator_ProcessorAbstract $processor) {
        $this->pageNumber = 1;

        $this->dictionary->fillVariables($this->report->getVariableElements(), $this);
        $this->report->generate($this, $processor);
        foreach ($this->instructions as $instruction) {
            $instruction->run($processor);
        }
    }

    /**
     * @return CReport_Adapter_Pdf_TCPDF
     */
    public function getPdf() {
        foreach ($this->getFonts() as $font) {
            CReport_Pdf_FontManager::instance()->addFont($font->getName(), $font->getPath());
            //$processor->addFont($font->getName(), $font->getPath());
        }
        $this->processor = new CReport_Generator_Processor_PdfProcessor($this->report);
        $this->isProcessingPdf = true;
        $this->generate($this->processor);
        $this->isProcessingPdf = false;
        $pdf = $this->processor->getOutput();
        $this->processor = null;
        // $pdf = CReport_Jasper_Instructions::get();

        return $pdf;
    }

    /**
     * @return CReport_Adapter_Excel_PhpSpreadsheet
     */
    public function getExcel() {
        $this->isProcessingExcel = true;

        $this->processor = new CReport_Generator_Processor_ExcelProcessor($this->report);
        $this->generate($this->processor);
        $this->isProcessingExcel = false;
        $excel = $this->processor->getOutput();
        $this->processor = null;

        // $pdf = CReport_Jasper_Instructions::get();

        return $excel;
    }

    /**
     * Generate the report and wrap the resulting grid as a CExporter export.
     *
     * @return CReport_Excel_Export
     */
    public function getExcelExport() {
        $this->isProcessingExcel = true;

        $this->processor = new CReport_Generator_Processor_ExcelProcessor($this->report);
        $this->generate($this->processor);
        $this->isProcessingExcel = false;
        /** @var CReport_Generator_Processor_ExcelProcessor $processor */
        $processor = $this->processor;
        $grid = $processor->getGrid();
        $this->processor = null;

        return new CReport_Excel_Export($grid);
    }
}
