<?php

trait CReport_Builder_Trait_Property_PatternPropertyTrait {
    protected $pattern;

    public function getPattern() {
        return $this->pattern;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setPattern(string $pattern) {
        $this->pattern = $pattern;

        return $this;
    }
}
