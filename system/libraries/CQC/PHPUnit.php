<?php

class CQC_PHPUnit {
    protected $testCases;

    public function __construct() {
        $this->testCases = [];
    }

    public function loadPath($path) {
        $files = CFile::allFiles($path);
        foreach ($files as $file) {
            $this->addTestCaseFile($file);
        }
    }

    protected function addTestCaseFile($file) {
        $this->testCases[] = new CQC_PHPUnit_TestCase((string) $file);

        return $this;
    }

    /**
     * Get array of CQC_PHPUnit_TestCase.
     *
     * @return CQC_PHPUnit_TestCase[]
     */
    public function getTestCases() {
        return $this->testCases;
    }
}
