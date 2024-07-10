<?php

trait CReport_Builder_Trait_Property_NamePropertyTrait {
    protected $name;

    public function getSplitType() {
        return $this->name;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setName(string $name) {
        $this->name = $name;

        return $this;
    }
}
