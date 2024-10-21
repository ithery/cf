<?php

trait CReport_Builder_Trait_Property_LeftPenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $leftPen;

    /**
     * @return CReport_Builder_Object_Pen
     */
    private function createLeftPenWhenNull() {
        if ($this->leftPen == null) {
            $this->leftPen = new CReport_Builder_Object_Pen();
        }

        return $this->leftPen;
    }

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getLeftPen() {
        return $this->leftPen;
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setLeftPen(CReport_Builder_Object_Pen $pen) {
        $this->leftPen = $pen;

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setLeftPenLineStyle($lineStyle) {
        $this->createLeftPenWhenNull()->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setLeftPenLineWidth($lineWidth) {
        $this->createLeftPenWhenNull()->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setLeftPenLineColor(string $color) {
        $this->createLeftPenWhenNull()->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getLeftPenLineStyle() {
        return c::optional($this->leftPen)->getLineStyle();
    }

    /**
     * @return float
     */
    public function getLeftPenLineWidth() {
        return c::optional($this->leftPen)->getLineWidth();
    }

    /**
     * @return string
     */
    public function getLeftPenLineColor() {
        return $this->leftPen->getLineColor();
    }
}
