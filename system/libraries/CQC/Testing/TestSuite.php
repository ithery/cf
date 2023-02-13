<?php

class CQC_Testing_TestSuite {
    /**
     * @var string
     */
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

    public function getPath() {
        return $this->path;
    }

    public function getRetries() {
        return 3;
    }

    public function getCommandOptions() {
        return '';
    }

    public function getFileMask() {
        return '*';
    }

    public function isCoverageEnabled() {
        return false;
    }

    public function getCoverageIndex() {
        return null;
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
