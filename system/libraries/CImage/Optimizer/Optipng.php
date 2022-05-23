<?php

class CImage_Optimizer_Optipng extends CImage_OptimizerAbstract {
    public $binaryName = 'optipng';

    public function canHandle(CImage_Image $image) {
        return $image->mime() === 'image/png';
    }
}
