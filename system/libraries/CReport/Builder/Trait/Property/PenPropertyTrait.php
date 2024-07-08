<?php

trait CReport_Builder_Trait_Property_PenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $pen;

    /**
     * @return CReport_Builder_Object_Pen
     */
    public function getPen() {
        return $this->pen;
    }

    /**
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return $this
     */
    public function setPen(CReport_Builder_Object_Pen $pen) {
        $this->pen = $pen;

        return $this;
    }

    /**
     * @param string $lineStyle
     *
     * @return $this
     */
    public function setPenLineStyle($lineStyle) {
        $this->pen->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setPenLineWidth($lineWidth) {
        $this->pen->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setPenLineColor(string $color) {
        $this->pen->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getPenLineStyle() {
        return $this->pen->getLineStyle();
    }

    /**
     * @return float
     */
    public function getPenLineWidth() {
        return $this->pen->getLineWidth();
    }

    /**
     * @return string
     */
    public function getPenLineColor() {
        return $this->pen->getLineColor();
    }
}
