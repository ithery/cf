<?php

class CImage_GoogleChart_QRCode extends CImage_GoogleChart_Chart {
    public function __construct($width = 150, $height = 150) {
        $this->setDimensions($width, $height);
        $this->setProperty('cht', 'qr');
    }

    public function setQRCode($QRCode) {
        $this->setProperty('chl', urlencode($QRCode));
    }

    /**
     * @param string $newOutputEncoding Please refer to the documentation for the acceptable values
     */
    public function setOutputEncoding($newOutputEncoding) {
        $this->setProperty('choe', $newOutputEncoding);
    }

    /**
     * @param string $newErrorCorrectionLevel Please refer to the documentation for the acceptable values
     * @param int    $newMargin               Please refer to the documentation for the acceptable values
     */
    public function setErrorCorrectionLevel($newErrorCorrectionLevel, $newMargin) {
        $this->setProperty('chld', $newErrorCorrectionLevel . '|' . $newMargin);
    }
}
