<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CImage_OptimizerAbstract implements CImage_OptimizerInterface {

    public $options = [];
    public $imagePath = '';
    public $binaryPath = '';

    public function __construct($options = []) {
        $this->setOptions($options);
    }

    public function binaryName() {
        return $this->binaryName;
    }

    public function setBinaryPath($binaryPath) {
        if (substr($binaryPath, -1) !== DIRECTORY_SEPARATOR) {
            $binaryPath = $binaryPath . DIRECTORY_SEPARATOR;
        }
        $this->binaryPath = $binaryPath;
        return $this;
    }

    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function setOptions(array $options = []) {
        $this->options = $options;
        return $this;
    }

    public function getCommand() {
        $optionString = implode(' ', $this->options);
        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString} " . escapeshellarg($this->imagePath);
    }

}
