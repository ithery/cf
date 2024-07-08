<?php

trait CReport_Builder_Trait_Property_BackgroundColorPropertyTrait {
    protected $backgroundColor;

    public function getBackgroundColor() {
        return $this->backgroundColor;
    }

    /**
     * @param mixed $backgroundColor
     *
     * @return $this
     */
    public function setBackgroundColor($backgroundColor) {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }
}
