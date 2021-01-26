<?php

class CRunner_FFMpeg_ProcessOutput {
    private $all;
    private $errors;
    private $out;

    public function __construct(array $all, array $errors, array $out) {
        $this->all = $all;
        $this->errors = $errors;
        $this->out = $out;
    }

    public function all() {
        return $this->all;
    }

    public function errors() {
        return $this->errors;
    }

    public function out() {
        return $this->out;
    }
}
