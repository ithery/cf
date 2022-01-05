<?php

trait CComponent_Testing_Concern_HasFunLittleUtilitiesTrait {
    public function dump() {
        echo $this->lastRenderedDom;

        return $this;
    }

    public function tap($callback) {
        $callback($this);

        return $this;
    }
}
