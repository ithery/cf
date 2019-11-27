<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CImage_Image {

    protected $pathToImage = '';

    public function __construct($pathToImage) {
        if (!file_exists($pathToImage)) {
            throw new InvalidArgumentException("`{$pathToImage}` does not exist");
        }
        $this->pathToImage = $pathToImage;
    }

    public function mime() {
        return mime_content_type($this->pathToImage);
    }

    public function path() {
        return $this->pathToImage;
    }

    public function extension() {
        $extension = pathinfo($this->pathToImage, PATHINFO_EXTENSION);
        return strtolower($extension);
    }

}
