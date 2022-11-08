<?php

class CQC_Testing_TestCase {
    protected $file;

    protected $methods = [];

    public function __construct($file) {
        $this->file = $file;
    }

    public function getRelativePath() {
        $testRoot = c::appRoot('');
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

    public function toArray() {
        return [
            'file' => $this->file,
            'path' => $this->getRelativePath(),
            'name' => $this->getName(),
            'updatedAt' => date('Y-m-d H:i:s', CFile::lastModified($this->file))
        ];
    }
}
