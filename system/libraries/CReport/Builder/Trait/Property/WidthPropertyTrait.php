<?php

trait CReport_Builder_Trait_Property_WidthPropertyTrait {
    protected $width;

    public function getWidth() {
        return $this->width;
    }

    /**
     * @param float $height
     *
     * @return $this
     */
    public function setWidth(float $width) {
        $this->width = $width;

        return $this;
    }
}
