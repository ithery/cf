<?php

class CImage_GoogleChart_Formula extends CImage_GoogleChart_Chart {
    /**
     * @param int $width  It is set by default to 0 because the server will size the png automatically
     * @param int $height It is set by default to 0 because the server will size the png automatically
     */
    public function __construct($width = 0, $height = 0) {
        $this->setDimensions($width, $height);
        $this->setProperty('cht', 'tx');
    }

    public function setLatexCode($latexCode) {
        $this->setProperty('chl', urlencode($latexCode));
    }

    public function setTextColor($textColor) {
        $this->setProperty('chco', $textColor);
    }

    public function getImgCode() {
        $code = '<img src="';
        $code .= $this->getUrl() . '"';
        $code .= 'alt="gChartPhp Chart"';
        if ($this->width) {
            $code .= ' width=' . $this->width;
        }
        if ($this->height) {
            $code .= ' height=' . $this->height;
        }
        $code .= '>';
        print $code;
    }
}
