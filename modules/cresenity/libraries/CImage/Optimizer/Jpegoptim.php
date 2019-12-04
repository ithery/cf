<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CImage_Optimizer_Jpegoptim extends CImage_OptimizerAbstract {

    public $binaryName = 'jpegoptim';

    public function canHandle(CImage_Image $image) {
        return $image->mime() === 'image/jpeg';
    }

}
