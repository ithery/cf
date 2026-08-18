<?php

trait CReport_Builder_Trait_Property_LineWidthPropertyTrait {
    /**
     * @var float
     */
    protected $lineWidth;

    public function getLineWidth() {
        return $this->lineWidth;
    }

    /**
     * @param float $lineWidth
     *
     * @return $this
     */
    public function setLineWidth(float $lineWidth) {
        $this->lineWidth = $lineWidth;

        return $this;
    }
}
