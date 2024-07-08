<?php

trait CReport_Builder_Trait_Property_LineStylePropertyTrait {
    protected $lineStyle;

    public function getLineStyle() {
        return $this->lineStyle;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setLineStyle(string $lineStyle) {
        $this->lineStyle = $lineStyle;

        return $this;
    }
}
