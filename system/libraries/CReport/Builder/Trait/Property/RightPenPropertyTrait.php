<?php

trait CReport_Builder_Trait_Property_RightPenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $rightPen;

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
        $this->rightPen->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setRightPenLineWidth($lineWidth) {
        $this->rightPen->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setRightPenLineColor(string $color) {
        $this->rightPen->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getRightPenLineStyle() {
        return $this->rightPen->getLineStyle();
    }

    /**
     * @return float
     */
    public function getRightPenLineWidth() {
        return $this->rightPen->getLineWidth();
    }

    /**
     * @return string
     */
    public function getRightPenLineColor() {
        return $this->rightPen->getLineColor();
    }
}
