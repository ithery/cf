<?php

class CImage_Optimizer_Svgo extends CImage_OptimizerAbstract {
    public $binaryName = 'svgo';

    public function canHandle(CImage_Image $image) {
        if ($image->extension() !== 'svg') {
            return false;
        }

        return in_array($image->mime(), [
            'text/html',
            'image/svg',
            'image/svg+xml',
            'text/plain',
        ]);
    }

    public function getCommand() {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
                . ' --input=' . escapeshellarg($this->imagePath)
                . ' --output=' . escapeshellarg($this->imagePath);
    }
}
