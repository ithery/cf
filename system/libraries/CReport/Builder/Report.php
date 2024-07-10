<?php

class CReport_Builder_Report implements CReport_Builder_Contract_JrXmlElementInterface {
    use CReport_Builder_Trait_HasChildrenElementTrait;
    use CReport_Builder_Trait_Property_FontPropertyTrait;

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
        $this->name = $name ?: 'CReport';
        $paperSize = CReport_Paper::$pageFormats['A4'];
        $this->children = [];
        $this->pageWidth = $paperSize[0];
        $this->pageHeight = $paperSize[1];
        $this->leftMargin = 20;
        $this->topMargin = 20;
        $this->bottomMargin = 20;
        $this->leftMargin = 20;
        $this->rightMargin = 20;
        $this->font = new CReport_Builder_Object_Font();
    }

    /**
     * @param float $width
     *
     * @return $this
     */
    public function setPageWidth($width) {
        $this->pageWidth = $width;

        return $this;
    }

    /**
     * @param float $height
     *
     * @return $this
     */
    public function setPageHeight($height) {
        $this->pageHeight = $height;

        return $this;
    }

    /**
     * @param float $topMargin
     *
     * @return $this
     */
    public function setTopMargin($topMargin) {
        $this->topMargin = $topMargin;

        return $this;
    }

    /**
     * @param float $bottomMargin
     *
     * @return $this
     */
    public function setBottomMargin($bottomMargin) {
        $this->bottomMargin = $bottomMargin;

        return $this;
    }

    /**
     * @param float $rightMargin
     *
     * @return $this
     */
    public function setRightMargin($rightMargin) {
        $this->rightMargin = $rightMargin;

        return $this;
    }

    /**
     * @param float $leftMargin
     *
     * @return $this
     */
    public function setLeftMargin($leftMargin) {
        $this->leftMargin = $leftMargin;

        return $this;
    }

    /**
     * @param float $columnWidth
     *
     * @return $this
     */
    public function setColumnWidth($columnWidth) {
        $this->columnWidth = $columnWidth;

        return $this;
    }

    public function setOrientation($orientation) {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * @return flaot
     */
    public function getPageWidth() {
        return $this->pageWidth;
    }

    /**
     * @return flaot
     */
    public function getPageHeight() {
        return $this->pageHeight;
    }

    /**
     * @return string
     */
    public function getOrientation() {
        return $this->orientation;
    }

    /**
     * @return flaot
     */
    public function getLeftMargin() {
        return $this->leftMargin;
    }

    /**
     * @return flaot
     */
    public function getTopMargin() {
        return $this->topMargin;
    }

    /**
     * @return flaot
     */
    public function getRightMargin() {
        return $this->rightMargin;
    }

    /**
     * @return flaot
     */
    public function getBottomMargin() {
        return $this->bottomMargin;
    }

    /**
     * @return CReport_Builder_Element_Group
     */
    public function addGroup() {
        $group = new CReport_Builder_Element_Group();
        $this->children[] = $group;

        return $group;
    }

    /**
     * @return CReport_Builder_Element_Group
     */
    public function addVariable() {
        $group = new CReport_Builder_Element_Variable();
        $this->children[] = $group;

        return $group;
    }

    protected function getDefaultFontTag() {
        return '<defaultFont '
        . ' name="' . $this->getFontName() . '"'
        . ' size="' . $this->getFontSize() . '"'
        . ' isBold="' . CReport_Builder_JrXmlEnum::getBoolEnum($this->fontIsBold()) . '"'
        . ' isItalic="' . CReport_Builder_JrXmlEnum::getBoolEnum($this->fontIsItalic()) . '"'
        . ' isUnderline="' . CReport_Builder_JrXmlEnum::getBoolEnum($this->fontIsUnderline()) . '"'
        . ' isStrikeThrough="' . CReport_Builder_JrXmlEnum::getBoolEnum($this->fontIsStrikeThrough()) . '"'
        . '/>';
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
        $body .= $this->getDefaultFontTag();
        foreach ($this->children as $child) {
            $body .= $child->toJrXml();
        }
        $closeTag = '</jasperReport>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function toJson() {
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        foreach ($this->children as $child) {
            if ($child instanceof CReport_Builder_Element_Group) {
                continue;
            }
            $child->generate($generator, $processor);
        }
    }
}
