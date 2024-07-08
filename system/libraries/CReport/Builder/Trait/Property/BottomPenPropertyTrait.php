<?php

trait CReport_Builder_Trait_Property_BottomPenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $bottomPen;

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getBottomPen() {
        return $this->bottomPen;
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setBottomPen(CReport_Builder_Object_Pen $pen) {
        $this->bottomPen = $pen;

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setBottomPenLineStyle($lineStyle) {
        $this->bottomPen->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setBottomPenLineWidth($lineWidth) {
        $this->bottomPen->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setBottomPenLineColor(string $color) {
        $this->bottomPen->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getBottomPenLineStyle() {
        return $this->bottomPen->getLineStyle();
    }

    /**
     * @return float
     */
    public function getBottomPenLineWidth() {
        return $this->bottomPen->getLineWidth();
    }

    /**
     * @return string
     */
    public function getBottomPenLineColor() {
        return $this->bottomPen->getLineColor();
    }
}
