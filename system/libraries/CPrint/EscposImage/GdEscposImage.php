<?php

/**
 * Implementation of EscposImage using the GD PHP plugin.
 */
class CPrint_EscposImage_GdEscposImage extends CPrint_EscposImage {
    /**
     * Load an image from disk, into memory, using GD.
     *
     * @param null|string $filename The filename to load from
     *
     * @throws Exception if the image format is not supported,
     *                   or the file cannot be opened
     */
    protected function loadImageData(string $filename = null) {
        if ($filename === null) {
            /* Set to blank image */
            return parent::loadImageData($filename);
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'png':
                $im = @imagecreatefrompng($filename);

                break;
            case 'jpg':
                $im = @imagecreatefromjpeg($filename);

                break;
            case 'gif':
                $im = @imagecreatefromgif($filename);

                break;
            default:
                throw new Exception('Image format not supported in GD');
        }
        $this->readImageFromGdResource($im);
    }

    /**
     * Load actual image pixels from GD resource.
     *
     * @param resource $im GD resource to use
     *
     * @throws Exception where the image can't be read
     */
    public function readImageFromGdResource($im) {
        if (!is_resource($im) && !$im instanceof \GdImage) {
            throw new Exception('Failed to load image.');
        } elseif (!EscposImage::isGdLoaded()) {
            throw new Exception(__FUNCTION__ . " requires 'gd' extension.");
        }
        /* Make a string of 1's and 0's */
        $imgHeight = imagesy($im);
        $imgWidth = imagesx($im);
        $imgData = str_repeat("\0", $imgHeight * $imgWidth);
        for ($y = 0; $y < $imgHeight; $y++) {
            for ($x = 0; $x < $imgWidth; $x++) {
                /* Faster to average channels, blend alpha and negate the image here than via filters (tested!) */
                $cols = imagecolorsforindex($im, imagecolorat($im, $x, $y));
                // 1 for white, 0 for black, ignoring transparency
                $greyness = (int) (($cols['red'] + $cols['green'] + $cols['blue']) / 3) >> 7;
                // 1 for black, 0 for white, taking into account transparency
                $black = (1 - $greyness) >> ($cols['alpha'] >> 6);
                $imgData[$y * $imgWidth + $x] = $black;
            }
        }
        $this->setImgWidth($imgWidth);
        $this->setImgHeight($imgHeight);
        $this->setImgData($imgData);
    }
}
