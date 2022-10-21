<?php

class CQC_PHPUnit {
    protected $suites;

    public function __construct() {
        $this->suites = [];
    }

    public function addSuite($path) {
        $this->suites[] = new CQC_PHPUnit_TestSuite($path);
    }

    /**
     * Get array of CQC_PHPUnit_TestSuite.
     *
     * @return CQC_PHPUnit_TestSuite[]
     */
    public function getTestSuites() {
        return $this->suites;
    }
}
