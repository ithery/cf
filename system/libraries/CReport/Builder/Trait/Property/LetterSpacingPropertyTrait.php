<?php

trait CReport_Builder_Trait_Property_LetterSpacingPropertyTrait {
    /**
     * @var float
     */
    protected $letterSpacing;

    /**
     * @return float
     */
    public function getLetterSpacing() {
        return $this->letterSpacing;
    }

    /**
     * @param float $letterSpacing
     *
     * @return $this
     */
    public function setLetterSpacing(float $letterSpacing) {
        $this->letterSpacing = $letterSpacing;

        return $this;
    }
}
