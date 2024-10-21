<?php

use Mike42\GfxPhp\Image;

/**
 * Implementation of EscposImage using only native PHP.
 */
class CPrinter_EscPos_EscposImage_NativeEscposImage extends CPrinter_EscPos_EscposImageAbstract {
    protected function loadImageData(string $filename = null) {
        $image = Image::fromFile($filename)->toRgb()->toBlackAndWhite();
        $imgHeight = $image->getHeight();
        $imgWidth = $image->getWidth();
        $imgData = str_repeat("\0", $imgHeight * $imgWidth);
        for ($y = 0; $y < $imgHeight; $y++) {
            for ($x = 0; $x < $imgWidth; $x++) {
                $imgData[$y * $imgWidth + $x] = $image->getPixel($x, $y) == 0 ? 0 : 1;
            }
        }
        $this->setImgWidth($imgWidth);
        $this->setImgHeight($imgHeight);
        $this->setImgData($imgData);
    }
}
