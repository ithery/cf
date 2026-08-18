<?php

trait CReport_Builder_Trait_Property_LineColorPropertyTrait {
    protected $lineColor;

    public function getLineColor() {
        return $this->lineColor;
    }

    /**
     * @param mixed $lineColor
     *
     * @return $this
     */
    public function setLineColor($lineColor) {
        $this->lineColor = $lineColor;

        return $this;
    }
}
