<?php

trait CReport_Builder_Trait_Property_TextAlignmentPropertyTrait {
    protected $textAlignment;

    public function getTextAlignment() {
        return $this->textAlignment;
    }

    /**
     * @param string $textAlignment
     *
     * @return $this
     */
    public function setTextAlignment(string $textAlignment) {
        $this->textAlignment = $textAlignment;

        return $this;
    }
}
