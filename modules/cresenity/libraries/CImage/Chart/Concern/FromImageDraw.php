<?php
use CImage_Chart_Constant as Constant;

trait CImage_Chart_Concern_FromImageDraw {
    /**
     * Load a PNG file and draw it over the chart
     *
     * @param int    $x
     * @param int    $y
     * @param string $fileName
     */
    public function drawFromPNG($x, $y, $fileName) {
        $this->drawFromPicture(1, $fileName, $x, $y);
    }

    /**
     * Load a GIF file and draw it over the chart
     *
     * @param int    $x
     * @param int    $y
     * @param string $fileName
     */
    public function drawFromGIF($x, $y, $fileName) {
        $this->drawFromPicture(2, $fileName, $x, $y);
    }

    /**
     * Load a JPEG file and draw it over the chart
     *
     * @param int    $x
     * @param int    $y
     * @param string $fileName
     */
    public function drawFromJPG($x, $y, $fileName) {
        $this->drawFromPicture(3, $fileName, $x, $y);
    }

    /**
     * Generic loader public function for external pictures
     *
     * @param int    $picType
     * @param string $fileName
     * @param int    $x
     * @param int    $y
     *
     * @return null|integer
     */
    public function drawFromPicture($picType, $fileName, $x, $y) {
        if (file_exists($fileName)) {
            list($width, $height) = $this->getPicInfo($fileName);
            if ($picType == 1) {
                $raster = imagecreatefrompng($fileName);
            } elseif ($picType == 2) {
                $raster = imagecreatefromgif($fileName);
            } elseif ($picType == 3) {
                $raster = imagecreatefromjpeg($fileName);
            } else {
                return 0;
            }
            $restoreShadow = $this->shadow;
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $this->shadow = false;
                if ($picType == 3) {
                    $this->drawFilledRectangle(
                        $x + $this->shadowX,
                        $y + $this->shadowY,
                        $x + $width + $this->shadowX,
                        $y + $height + $this->shadowY,
                        [
                            'r' => $this->shadowR,
                            'g' => $this->shadowG,
                            'b' => $this->shadowB,
                            'alpha' => $this->shadowA
                        ]
                    );
                } else {
                    $tranparentID = imagecolortransparent($raster);
                    for ($xc = 0; $xc <= $width - 1; $xc++) {
                        for ($yc = 0; $yc <= $height - 1; $yc++) {
                            $rGBa = imagecolorat($raster, $xc, $yc);
                            $values = imagecolorsforindex($raster, $rGBa);
                            if ($values['alpha'] < 120) {
                                $alphaFactor = floor(
                                    ($this->shadowA / 100) * ((100 / 127) * (127 - $values['alpha']))
                                );
                                $this->drawalphaPixel(
                                    $x + $xc + $this->shadowX,
                                    $y + $yc + $this->shadowY,
                                    $alphaFactor,
                                    $this->shadowR,
                                    $this->shadowG,
                                    $this->shadowB
                                );
                            }
                        }
                    }
                }
            }
            $this->shadow = $restoreShadow;
            imagecopy($this->picture, $raster, $x, $y, 0, 0, $width, $height);
            imagedestroy($raster);
        }
    }
}
