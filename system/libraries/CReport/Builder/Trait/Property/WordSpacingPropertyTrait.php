<?php

trait CReport_Builder_Trait_Property_WordSpacingPropertyTrait {
    /**
     * @var float
     */
    protected $wordSpacing;

    /**
     * @return float
     */
    public function getWordSpacing() {
        return $this->wordSpacing;
    }

    /**
     * @param float $wordSpacing
     *
     * @return $this
     */
    public function setWordSpacing(float $wordSpacing) {
        $this->wordSpacing = $wordSpacing;

        return $this;
    }
}
