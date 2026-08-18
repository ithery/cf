<?php

trait CReport_Builder_Trait_Property_ModePropertyTrait {
    protected $mode;

    public function getMode() {
        return $this->mode;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setMode(string $mode) {
        $this->mode = $mode;

        return $this;
    }
}
