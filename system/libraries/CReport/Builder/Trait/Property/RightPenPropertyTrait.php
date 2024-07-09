<?php

trait CReport_Builder_Trait_Property_RightPenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $rightPen;

    /**
     * @return CReport_Builder_Object_Pen
     */
    private function createRightPenWhenNull() {
        if ($this->rightPen == null) {
            $this->rightPen = new CReport_Builder_Object_Pen();
        }

        return $this->rightPen;
    }

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getRightPen() {
        return $this->rightPen;
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setRightPen(CReport_Builder_Object_Pen $pen) {
        $this->rightPen = $pen;

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setRightPenLineStyle($lineStyle) {
        $this->createRightPenWhenNull()->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setRightPenLineWidth($lineWidth) {
        $this->createRightPenWhenNull()->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setRightPenLineColor(string $color) {
        $this->createRightPenWhenNull()->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getRightPenLineStyle() {
        return c::optional($this->rightPen)->getLineStyle();
    }

    /**
     * @return float
     */
    public function getRightPenLineWidth() {
        return c::optional($this->rightPen)->getLineWidth();
    }

    /**
     * @return string
     */
    public function getRightPenLineColor() {
        return c::optional($this->rightPen)->getLineColor();
    }
}
