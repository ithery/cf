<?php

trait CReport_Builder_Trait_Property_TopPenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $topPen;

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getTopPen() {
        return $this->topPen;
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setTopPen(CReport_Builder_Object_Pen $pen) {
        $this->topPen = $pen;

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setTopPenLineStyle($lineStyle) {
        $this->topPen->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setTopPenLineWidth($lineWidth) {
        $this->topPen->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setTopPenLineColor(string $color) {
        $this->topPen->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getTopPenLineStyle() {
        return $this->topPen->getLineStyle();
    }

    /**
     * @return float
     */
    public function getTopPenLineWidth() {
        return $this->topPen->getLineWidth();
    }

    /**
     * @return string
     */
    public function getTopPenLineColor() {
        return $this->topPen->getLineColor();
    }
}
