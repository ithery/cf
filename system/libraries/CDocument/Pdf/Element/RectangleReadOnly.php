<?php

class CDocument_Pdf_Element_RectangleReadOnly extends CDocument_Pdf_Element_Rectangle {
    /**
     * Throws an error because of the read only nature of this object.
     */
    private function throwReadOnlyError() {
        throw new BadMethodCallException('RectangleReadOnly: this Rectangle is read only.');
    }

    public function setLeft(float $llx) {
        $this->throwReadOnlyError();
    }

    public function setRight(float $urx) {
        $this->throwReadOnlyError();
    }

    public function setTop(float $ury) {
        $this->throwReadOnlyError();
    }

    public function setBottom(float $lly) {
        $this->throwReadOnlyError();
    }

    public function normalize() {
        $this->throwReadOnlyError();
    }

    public function setBackgroundColor(CColor_FormatAbstract $value) {
        $this->throwReadOnlyError();
    }

    public function setGrayFill(float $value) {
        $this->throwReadOnlyError();
    }

    public function setBorder(int $border) {
        $this->throwReadOnlyError();
    }

    public function setUseVariableBorders(bool $useVariableBorders) {
        $this->throwReadOnlyError();
    }

    public function enableBorderSide(int $side) {
        $this->throwReadOnlyError();
    }

    public function disableBorderSide(int $side) {
        $this->throwReadOnlyError();
    }

    public function setBorderWidth(float $borderWidth) {
        $this->throwReadOnlyError();
    }

    public function setBorderWidthLeft(float $borderWidthLeft) {
        $this->throwReadOnlyError();
    }

    public function setBorderWidthRight(float $borderWidthRight) {
        $this->throwReadOnlyError();
    }

    public function setBorderWidthTop(float $borderWidthTop) {
        $this->throwReadOnlyError();
    }

    public function setBorderWidthBottom(float $borderWidthBottom) {
        $this->throwReadOnlyError();
    }

    public function setBorderColor(CColor_FormatAbstract $borderColor) {
        $this->throwReadOnlyError();
    }

    public function setBorderColorLeft(CColor_FormatAbstract $borderColorLeft) {
        $this->throwReadOnlyError();
    }

    public function setBorderColorRight(CColor_FormatAbstract $borderColorRight) {
        $this->throwReadOnlyError();
    }

    public function setBorderColorTop(CColor_FormatAbstract $borderColorTop) {
        $this->throwReadOnlyError();
    }

    public function setBorderColorBottom(CColor_FormatAbstract $borderColorBottom) {
        $this->throwReadOnlyError();
    }

    public function cloneNonPositionParameters(CDocument_Pdf_Element_Rectangle $rect) {
        $this->throwReadOnlyError();
    }

    public function softCloneNonPositionParameters(CDocument_Pdf_Element_Rectangle $rect) {
        $this->throwReadOnlyError();
    }

    /**
     * @return string
     */
    public function __toString() {
        $string = 'RectangleReadOnly: ';
        $string .= $this->getWidth();
        $string .= 'x';
        $string .= $this->getHeight();
        $string .= ' (rot: ';
        $string .= $this->rotation;
        $string .= ' degrees)';

        return $string;
    }
}
