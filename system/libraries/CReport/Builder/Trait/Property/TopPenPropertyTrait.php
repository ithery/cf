<?php

trait CReport_Builder_Trait_Property_TopPenPropertyTrait {
    /**
     * @var CReport_Builder_Object_Pen
     */
    protected $topPen;

    /**
     * @return CReport_Builder_Object_Pen
     */
    private function createTopPenWhenNull() {
        if ($this->topPen == null) {
            $this->topPen = new CReport_Builder_Object_Pen();
        }

        return $this->topPen;
    }

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
        $this->createTopPenWhenNull()->setLineStyle($lineStyle);

        return $this;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setTopPenLineWidth($lineWidth) {
        $this->createTopPenWhenNull()->setLineWidth($lineWidth);

        return $this;
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setTopPenLineColor(string $color) {
        $this->createTopPenWhenNull()->setLineColor($color);

        return $this;
    }

    /**
     * @return string
     */
    public function getTopPenLineStyle() {
        return c::optional($this->topPen)->getLineStyle();
    }

    /**
     * @return float
     */
    public function getTopPenLineWidth() {
        return c::optional($this->topPen)->getLineWidth();
    }

    /**
     * @return string
     */
    public function getTopPenLineColor() {
        return c::optional($this->topPen)->getLineColor();
    }
}
