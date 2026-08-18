<?php

trait CReport_Builder_Trait_Property_RadiusPropertyTrait {
    protected $radius;

    public function getRadius() {
        return $this->radius;
    }

    /**
     * @param float $radius
     *
     * @return $this
     */
    public function setRadius(float $radius) {
        $this->radius = $radius;

        return $this;
    }
}
