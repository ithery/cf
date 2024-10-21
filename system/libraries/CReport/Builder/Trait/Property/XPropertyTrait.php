<?php

trait CReport_Builder_Trait_Property_XPropertyTrait {
    protected $x;

    public function getX() {
        return $this->x;
    }

    /**
     * @param float $height
     *
     * @return $this
     */
    public function setX(float $x) {
        $this->x = $x;

        return $this;
    }
}
