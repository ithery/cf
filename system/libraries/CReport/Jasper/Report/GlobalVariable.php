<?php

class CReport_Jasper_Report_GlobalVariable {
    protected $reportCount;

    protected $pageCount;

    protected $pageNumber;

    protected $columnNumber;

    protected $columnCount;

    public function __construct() {
        $this->columnNumber = 0;
        $this->reportCount = 0;
        $this->pageCount = 0;
        $this->pageNumber = 0;
        $this->columnCount = 0;
    }
}
