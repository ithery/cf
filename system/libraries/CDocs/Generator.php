<?php

class CDocs_Generator {
    protected $inputDir;

    protected $outputDir;

    protected $outputHandler;

    public function __construct($inputDir, $outputDir, $options = []) {
        $this->inputDir = $inputDir;
        $this->outputDir = $outputDir;
    }

    public function output($type, $line) {
        if ($this->outputHandler) {
            return call_user_func_array($this->outputHandler, [$type, $line]);
        }
    }

    public function info($line) {
        return $this->output('info', $line);
    }

    public function generate() {
    }
}
