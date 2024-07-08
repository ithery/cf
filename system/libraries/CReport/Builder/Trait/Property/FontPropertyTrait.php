<?php

trait CReport_Builder_Trait_Property_FontPropertyTrait {
    /**
     * @var CReport_Builder_Object_Font
     */
    protected $font;

    /**
     * @return CReport_Builder_Object_Font
     */
    public function getFont() {
        return $this->font;
    }

    /**
     * @param CReport_Builder_Object_Font $font
     *
     * @return $this
     */
    public function setFont(CReport_Builder_Object_Font $font) {
        $this->font = $font;

        return $this;
    }

    /**
     * @param string $fontName
     *
     * @return $this
     */
    public function setFontName($fontName) {
        $this->font->setFontName($fontName);

        return $this;
    }

    /**
     * @param float $fontSize
     *
     * @return $this
     */
    public function setFontSize($fontSize) {
        $this->font->setFontSize($fontSize);

        return $this;
    }

    /**
     * @param bool $isBold
     *
     * @return $this
     */
    public function setFontBold(bool $isBold = true) {
        $this->font->setBold($isBold);

        return $this;
    }

    /**
     * @param bool $isItalic
     *
     * @return $this
     */
    public function setFontItalic(bool $isItalic = true) {
        $this->font->setItalic($isItalic);

        return $this;
    }

    /**
     * @param bool $isUnderline
     *
     * @return $this
     */
    public function setFontUnderline(bool $isUnderline = true) {
        $this->font->setUnderline($isUnderline);

        return $this;
    }

    /**
     * @param bool $isStrikeThrough
     *
     * @return $this
     */
    public function setFontStrikeThrough(bool $isStrikeThrough = true) {
        $this->font->setStrikeThrough($isStrikeThrough);

        return $this;
    }

    /**
     * @return string
     */
    public function getFontName() {
        return $this->font->getFontName();
    }

    /**
     * @return float
     */
    public function getFontSize() {
        return $this->font->getFontSize();
    }

    /**
     * @return bool
     */
    public function fontIsBold() {
        return $this->font->isBold();
    }

    /**
     * @return bool
     */
    public function fontIsItalic() {
        return $this->font->isItalic();
    }

    /**
     * @return bool
     */
    public function fontIsUnderline() {
        return $this->font->isUnderline();
    }

    /**
     * @return bool
     */
    public function fontIsStrikeThrough() {
        return $this->font->isStrikeThrough();
    }
}
