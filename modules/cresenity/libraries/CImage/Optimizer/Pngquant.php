<?php

class CImage_Optimizer_Pngquant extends CImage_OptimizerAbstract {
    public $binaryName = 'pngquant';

    public function canHandle(CImage_Image $image) {
        return $image->mime() === 'image/png';
    }

    public function getCommand() {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
                . ' ' . escapeshellarg($this->imagePath)
                . ' --output=' . escapeshellarg($this->imagePath);
    }
}
