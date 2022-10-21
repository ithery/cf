<?php

class CQC_Testing_TestSuite {
    protected $path;

    /**
     * @var CQC_Testing_TestCase[]
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
        $this->testCases[] = new CQC_Testing_TestCase((string) $file);

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return CQC_Testing_TestCase[]
     */
    public function getTestCases() {
        return $this->testCases;
    }
}
