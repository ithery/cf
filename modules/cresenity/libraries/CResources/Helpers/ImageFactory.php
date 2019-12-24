<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Helpers_ImageFactory {

    /**
     * 
     * @param string $path
     * @return CImage_Image
     */
    public static function load($path) {
        return CImage::image($path)
                        ->useImageDriver(CF::config('resource.imageDriver'));
    }

}
