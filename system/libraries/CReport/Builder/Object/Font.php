<?php

class CReport_Builder_Object_Font implements CReport_Builder_Contract_JrXmlElementInterface {
    /**
     * @var string
     */
    protected $fontName;

    /**
     * @var float
     */
    protected $fontSize;

    /**
     * @var bool
     */
    protected $isBold;

    /**
     * @var bool
     */
    protected $isStrikeThrough;

    /**
     * @var bool
     */
    protected $isUnderline;

    /**
     * @var bool
     */
    protected $isItalic;

    public function __construct() {
        $this->fontName = 'Helvetica';
        $this->fontSize = 12;
        $this->isBold = false;
        $this->isItalic = false;
        $this->isUnderline = false;
        $this->isStrikeThrough = false;
    }

    /**
     * @param string $fontName
     *
     * @return $this
     */
    public function setFontName($fontName) {
        $this->fontName = $fontName;

        return $this;
    }

    /**
     * @param float $fontSize
     *
     * @return $this
     */
    public function setFontSize($fontSize) {
        $this->fontSize = $fontSize;

        return $this;
    }

    /**
     * @param bool $isBold
     *
     * @return $this
     */
    public function setBold($isBold) {
        $this->isBold = $isBold;

        return $this;
    }

    /**
     * @param bool $isUnderline
     *
     * @return $this
     */
    public function setUnderline($isUnderline) {
        $this->isUnderline = $isUnderline;

        return $this;
    }

    /**
     * @param bool $isItalic
     *
     * @return $this
     */
    public function setItalic($isItalic) {
        $this->isItalic = $isItalic;

        return $this;
    }

    /**
     * @param bool $isStrikeThrough
     *
     * @return $this
     */
    public function setStrikeThrough($isStrikeThrough) {
        $this->isStrikeThrough = $isStrikeThrough;

        return $this;
    }

    /**
     * @return string
     */
    public function getFontName() {
        return $this->fontName;
    }

    /**
     * @return float
     */
    public function getFontSize() {
        return $this->fontSize;
    }

    /**
     * @return bool
     */
    public function isBold() {
        return $this->isBold;
    }

    /**
     * @return bool
     */
    public function isUnderline() {
        return $this->isUnderline;
    }

    /**
     * @return bool
     */
    public function isItalic() {
        return $this->isItalic;
    }

    /**
     * @return bool
     */
    public function isStrikeThrough() {
        return $this->isStrikeThrough;
    }

    public function toJrXml() {
        $tag = '<font';
        if ($this->fontName) {
            $tag .= ' name="' . $this->fontName . '"';
        }

        if ($this->fontSize) {
            $tag .= ' size="' . $this->fontSize . '"';
        }

        $tag .= ' isBold="' . CReport_Builder_PhpToJrXmlEnum::getBoolEnum($this->isBold) . '"';
        $tag .= ' isUnderline="' . CReport_Builder_PhpToJrXmlEnum::getBoolEnum($this->isUnderline) . '"';
        $tag .= ' isStrikeThrough="' . CReport_Builder_PhpToJrXmlEnum::getBoolEnum($this->isStrikeThrough) . '"';
        $tag .= ' isItalic="' . CReport_Builder_PhpToJrXmlEnum::getBoolEnum($this->isItalic) . '"';
        $tag .= '/>';

        return $tag;
    }
}
