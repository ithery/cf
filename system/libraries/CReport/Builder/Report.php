<?php

class CReport_BUilder_Report {
    protected $name;

    protected $pageWidth;

    protected $pageHeight;

    protected $columnWidth;

    protected $leftMargin;

    protected $rightMargin;

    protected $topMargin;

    protected $bottomMargin;

    protected $children;

    protected $orientation;

    public function __construct($name = null) {
        $this->name = $name ?: 'Report';
        $paperSize = CReport_Paper::$pageFormats['A4'];
        $this->children = [];
        $this->pageWidth = $paperSize[0];
        $this->pageHeight = $paperSize[1];
        $this->leftMargin = 20;
        $this->topMargin = 20;
        $this->bottomMargin = 20;
        $this->leftMargin = 20;
        $this->rightMargin = 20;
    }

    public function setPageWidth($width) {
        $this->pageWidth = $width;

        return $this;
    }

    public function setPageHeight($height) {
        $this->pageHeight = $height;

        return $this;
    }

    public function setOrientation($orientation) {
        $this->orientation = $orientation;

        return $this;
    }

    public function toJrXml() {
        $openTag = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . '<jasperReport xmlns="http://jasperreports.sourceforge.net/jasperreports"' . PHP_EOL
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL
            . 'xsi:schemaLocation="http://jasperreports.sourceforge.net/jasperreports http://jasperreports.sourceforge.net/xsd/jasperreport.xsd"' . PHP_EOL;
        $openTag .= 'name="' . $this->name . '"' . PHP_EOL;
        $openTag .= 'pageWidth="' . $this->pageWidth . '"' . PHP_EOL;
        $openTag .= 'pageHeight="' . $this->pageHeight . '"' . PHP_EOL;
        $openTag .= 'columnWidth="' . $this->columnWidth . '"' . PHP_EOL;
        $openTag .= 'leftMargin="' . $this->leftMargin . '"' . PHP_EOL;
        $openTag .= 'rightMargin="' . $this->rightMargin . '"' . PHP_EOL;
        $openTag .= 'topMargin="' . $this->topMargin . '"' . PHP_EOL;
        $openTag .= 'bottomMargin="' . $this->bottomMargin . '"' . PHP_EOL;
        $openTag .= 'orientation="' . ($this->orientation == CReport_Paper::ORIENTATION_LANDSCAPE ? 'Landscape' : 'Portrait') . '"' . PHP_EOL;
        $openTag .= '>';
        $body = '';
        foreach ($this->children as $child) {
            $body .= $child->toJrXml();
        }
        $closeTag = '</jasperReport>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function toJson() {
    }
}
