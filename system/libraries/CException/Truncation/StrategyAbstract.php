<?php

abstract class CException_Truncation_StrategyAbstract implements CException_Contract_TruncationStrategyInterface {
    /**
     * @var CException_Truncation_ReportTrimmer
     */
    protected $reportTrimmer;

    public function __construct(CException_Truncation_ReportTrimmer $reportTrimmer) {
        $this->reportTrimmer = $reportTrimmer;
    }
}
