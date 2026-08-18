<?php

trait CReport_Builder_Trait_Property_ForegroundColorPropertyTrait {
    protected $foregroundColor;

    public function getForegroundColor() {
        return $this->foregroundColor;
    }

    /**
     * @param mixed $foregroundColor
     *
     * @return $this
     */
    public function setForegroundColor($foregroundColor) {
        $this->foregroundColor = $foregroundColor;

        return $this;
    }
}
