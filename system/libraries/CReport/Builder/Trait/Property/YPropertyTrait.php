<?php

trait CReport_Builder_Trait_Property_YPropertyTrait {
    protected $y;

    public function getY() {
        return $this->y;
    }

    /**
     * @param float $height
     *
     * @return $this
     */
    public function setY(float $y) {
        $this->y = $y;

        return $this;
    }
}
