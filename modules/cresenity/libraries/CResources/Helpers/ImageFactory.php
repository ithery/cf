<?php

class CResources_Helpers_ImageFactory {
    /**
     * @param string $path
     *
     * @return CImage_Image
     */
    public static function load($path) {
        return CImage::image($path)
            ->useImageDriver(CF::config('resource.image_driver'));
    }
}
