<?php

trait CReport_Builder_Trait_Property_SplitTypePropertyTrait {
    protected $splitType;

    public function getSplitType() {
        return $this->splitType;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setSplitType(string $splitType) {
        $this->splitType = $splitType;

        return $this;
    }
}
