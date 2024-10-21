<?php

trait CReport_Builder_Trait_Property_SrcPropertyTrait {
    protected $src;

    public function getSrc() {
        return $this->src;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setSrc(string $src) {
        $this->src = $src;

        return $this;
    }
}
