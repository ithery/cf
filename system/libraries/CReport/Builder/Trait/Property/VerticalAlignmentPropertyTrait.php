<?php

trait CReport_Builder_Trait_Property_VerticalAlignmentPropertyTrait {
    protected $verticalAlignment;

    public function getVerticalAlignment() {
        return $this->verticalAlignment;
    }

    /**
     * @param string $verticalAlignment
     *
     * @return $this
     */
    public function setVerticalAlignment(string $verticalAlignment) {
        $this->verticalAlignment = $verticalAlignment;

        return $this;
    }
}
