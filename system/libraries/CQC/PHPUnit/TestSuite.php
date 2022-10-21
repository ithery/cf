<?php

class CQC_PHPUnit_TestSuite {
    protected $path;

    /**
     * @var CQC_PHPUnit_TestCase[]
     */
    protected $testCases;

    protected $name;

    public function __construct($path, $name = null) {
        $this->path = $path;
        $files = CFile::allFiles($path);
        foreach ($files as $file) {
            $this->addTestCaseFile($file);
        }

        $this->name = $name ?: carr::last(explode('/', rtrim($path, '/')));
    }

    protected function addTestCaseFile($file) {
        $this->testCases[] = new CQC_PHPUnit_TestCase((string) $file);

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return CQC_PHPUnit_TestCase[]
     */
    public function getTestCases() {
        return $this->testCases;
    }
}
