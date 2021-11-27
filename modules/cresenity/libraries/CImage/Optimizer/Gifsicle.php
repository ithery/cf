<?php

class CImage_Optimizer_Gifsicle extends CImage_OptimizerAbstract {
    public $binaryName = 'gifsicle';

    public function canHandle(CImage_Image $image) {
        return $image->mime() === 'image/gif';
    }
}
