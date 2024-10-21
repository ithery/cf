<?php

trait CReport_Builder_Trait_Property_HorizontalAlignmentPropertyTrait {
    protected $horizontalAlignment;

    public function getHorizontalAlignment() {
        return $this->horizontalAlignment;
    }

    /**
     * @param string $horizontalAlignment
     *
     * @return $this
     */
    public function setHorizontalAlignment(string $horizontalAlignment) {
        $this->horizontalAlignment = $horizontalAlignment;

        return $this;
    }
}
