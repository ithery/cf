<?php

trait CReport_Builder_Trait_Property_HeightPropertyTrait {
    protected $height;

    public function getHeight() {
        return $this->height;
    }

    /**
     * @param float $height
     *
     * @return $this
     */
    public function setHeight(float $height) {
        $this->height = $height;

        return $this;
    }
}
