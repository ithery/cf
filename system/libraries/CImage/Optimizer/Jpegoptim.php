<?php

class CImage_Optimizer_Jpegoptim extends CImage_OptimizerAbstract {
    public $binaryName = 'jpegoptim';

    public function canHandle(CImage_Image $image) {
        return $image->mime() === 'image/jpeg';
    }
}
