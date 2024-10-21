<?php

trait CReport_Builder_Trait_Property_StylePropertyTrait {
    protected $style;

    public function getStyle() {
        return $this->style;
    }

    /**
     * @param string $style
     *
     * @return $this
     */
    public function setStyle(string $style) {
        $this->style = $style;

        return $this;
    }

    public function applyStyle(CReport_Generator $generator) {
        $element = $generator->getStyle($this->style);
        if ($element) {
            if ($element->getBackgroundColor()) {
                if (c::hasTrait($this, CReport_Builder_Trait_Property_BackgroundColorPropertyTrait::class)) {
                    $this->setBackgroundColor($element->getBackgroundColor());
                }
            }
            if ($element->getForegroundColor()) {
                if (c::hasTrait($this, CReport_Builder_Trait_Property_ForegroundColorPropertyTrait::class)) {
                    $this->setForegroundColor($element->getForegroundColor());
                }
            }
            if ($element->getBox()) {
                if (c::hasTrait($this, CReport_Builder_Trait_Property_BoxPropertyTrait::class)) {
                    $this->setBox($element->getBox());
                }
            }
        }
    }
}
