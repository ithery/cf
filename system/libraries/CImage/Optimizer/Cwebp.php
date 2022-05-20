<?php

class CImage_Optimizer_Cwebp extends CImage_OptimizerAbstract {
    public $binaryName = 'cwebp';

    public function canHandle(CImage_Image $image) {
        return $image->mime() === 'image/webp';
    }

    public function getCommand() {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
                . ' ' . escapeshellarg($this->imagePath)
                . ' -o ' . escapeshellarg($this->imagePath);
    }
}
