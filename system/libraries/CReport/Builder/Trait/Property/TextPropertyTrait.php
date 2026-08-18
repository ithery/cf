<?php

trait CReport_Builder_Trait_Property_TextPropertyTrait {
    protected $text;

    public function getText() {
        return $this->text;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setText(string $text) {
        $this->text = $text;

        return $this;
    }
}
