<?php

class CQC_PHPUnit_TestCase {
    protected $file;

    protected $methods = [];

    public function __construct($file) {
        $this->file = $file;
        $suite = new PHPUnit\Framework\TestSuite();
        $suite->addTestFile($file);
    }

    public function getRelativePath() {
        $testRoot = c::appRoot('default/tests');
        $path = $this->file;
        if (cstr::startsWith($path, $testRoot)) {
            $path = substr($path, strlen($testRoot));
        }

        return $path;
    }

    public function getName() {
        $name = $this->getRelativePath();
        if (cstr::endsWith($name, '.php')) {
            $name = substr($name, 0, -4);
        }

        return $name;
    }

    public function getMethods() {
        return $this->methods;
    }
}
