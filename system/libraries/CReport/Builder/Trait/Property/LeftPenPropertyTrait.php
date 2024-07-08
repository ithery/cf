<?php

trait CReport_Builder_Trait_Property_LeftPenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $leftPen;

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
        $this->leftPen->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setLeftPenLineWidth($lineWidth) {
        $this->leftPen->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setLeftPenLineColor(string $color) {
        $this->leftPen->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getLeftPenLineStyle() {
        return $this->leftPen->getLineStyle();
    }

    /**
     * @return float
     */
    public function getLeftPenLineWidth() {
        return $this->leftPen->getLineWidth();
    }

    /**
     * @return string
     */
    public function getLeftPenLineColor() {
        return $this->leftPen->getLineColor();
    }
}
