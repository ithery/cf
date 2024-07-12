<?php

trait CReport_Builder_Trait_Property_BoxPropertyTrait {
    /**
     * @var CReport_Builder_Object_Box
     */
    protected $box;

    /**
     * @return CReport_Builder_Object_Box
     */
    public function getBox() {
        return $this->box;
    }

    /**
     * @return $this
     */
    public function setBox(CReport_Builder_Object_Box $box) {
        $this->box = $box;

        return $this;
    }

    /**
     * @return CReport_Builder_Object_Padding
     */
    public function getPadding() {
        return $this->box;
    }

    /**
     * @param CReport_Builder_Object_Padding $padding
     *
     * @return $this
     */
    public function setPadding(CReport_Builder_Object_Padding $padding) {
        $this->box->setPadding($padding);

        return $this;
    }

    /**
     * @param float $top
     *
     * @return $this
     */
    public function setPaddingTop($top) {
        $this->box->setPaddingTop($top);

        return $this;
    }

    /**
     * @param float $right
     *
     * @return $this
     */
    public function setPaddingRight($right) {
        $this->box->setPaddingRight($right);

        return $this;
    }

    /**
     * @param float $bottom
     *
     * @return $this
     */
    public function setPaddingBottom($bottom) {
        $this->box->setPaddingBottom($bottom);

        return $this;
    }

    /**
     * @param float $left
     *
     * @return $this
     */
    public function setPaddingLeft($left) {
        $this->box->setPaddingLeft($left);

        return $this;
    }

    /**
     * @return float
     */
    public function getPaddingTop() {
        return $this->box->getPaddingTop();
    }

    /**
     * @return float
     */
    public function getPaddingRight() {
        return $this->box->getPaddingRight();
    }

    /**
     * @return float
     */
    public function getPaddingBottom() {
        return $this->box->getPaddingBottom();
    }

    /**
     * @return float
     */
    public function getPaddingLeft() {
        return $this->box->getPaddingLeft();
    }

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getPen() {
        return $this->box->getPen();
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setPen(CReport_Builder_Object_Pen $pen) {
        $this->box->setPen($pen);

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setPenLineStyle($lineStyle) {
        $this->box->setPenLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setPenLineWidth($lineWidth) {
        $this->box->setPenLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setPenLineColor(string $color) {
        $this->box->setPenLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getPenLineStyle() {
        return $this->box->getPenLineStyle();
    }

    /**
     * @return float
     */
    public function getPenLineWidth() {
        return $this->box->getPenLineWidth();
    }

    /**
     * @return string
     */
    public function getPenLineColor() {
        return $this->box->getPenLineColor();
    }

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getTopPen() {
        return $this->box->getTopPen();
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setTopPen(CReport_Builder_Object_Pen $pen) {
        $this->box->setTopPen($pen);

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setTopPenLineStyle($lineStyle) {
        $this->box->setTopPenLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setTopPenLineWidth($lineWidth) {
        $this->box->setTopPenLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setTopPenLineColor(string $color) {
        $this->box->setTopPenLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getTopPenLineStyle() {
        return $this->box->getTopPenLineStyle();
    }

    /**
     * @return float
     */
    public function getTopPenLineWidth() {
        return $this->box->getTopPenLineWidth();
    }

    /**
     * @return string
     */
    public function getTopPenLineColor() {
        return $this->box->getTopPenLineColor();
    }

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getRightPen() {
        return $this->box->getRightPen();
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setRightPen(CReport_Builder_Object_Pen $pen) {
        $this->box->setRightPen($pen);

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setRightPenLineStyle($lineStyle) {
        $this->box->setRightPenLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setRightPenLineWidth($lineWidth) {
        $this->box->setRightPenLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setRightPenLineColor(string $color) {
        $this->box->setRightPenLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getRightPenLineStyle() {
        return $this->box->getRightPenLineStyle();
    }

    /**
     * @return float
     */
    public function getRightPenLineWidth() {
        return $this->box->getRightPenLineWidth();
    }

    /**
     * @return string
     */
    public function getRightPenLineColor() {
        return $this->box->getRightPenLineColor();
    }

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getBottomPen() {
        return $this->box->getBottomPen();
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setBottomPen(CReport_Builder_Object_Pen $pen) {
        $this->box->setBottomPen($pen);

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setBottomPenLineStyle($lineStyle) {
        $this->box->setBottomPenLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setBottomPenLineWidth($lineWidth) {
        $this->box->setBottomPenLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setBottomPenLineColor(string $color) {
        $this->box->setBottomPenLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getBottomPenLineStyle() {
        return $this->box->getBottomPenLineStyle();
    }

    /**
     * @return float
     */
    public function getBottomPenLineWidth() {
        return $this->box->getBottomPenLineWidth();
    }

    /**
     * @return string
     */
    public function getBottomPenLineColor() {
        return $this->box->getBottomPenLineColor();
    }

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getLeftPen() {
        return $this->box->getLeftPen();
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setLeftPen(CReport_Builder_Object_Pen $pen) {
        $this->box->setLeftPen($pen);

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setLeftPenLineStyle($lineStyle) {
        $this->box->setLeftPenLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setLeftPenLineWidth($lineWidth) {
        $this->box->setLeftPenLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setLeftPenLineColor(string $color) {
        $this->box->setLeftPenLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getLeftPenLineStyle() {
        return $this->box->getLeftPenLineStyle();
    }

    /**
     * @return float
     */
    public function getLeftPenLineWidth() {
        return $this->box->getLeftPenLineWidth();
    }

    /**
     * @return string
     */
    public function getLeftPenLineColor() {
        return $this->box->getLeftPenLineColor();
    }
}
