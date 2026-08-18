<?php

trait CReport_Builder_Trait_Property_IsStretchWithOverflowPropertyTrait {
    /**
     * @var bool
     */
    protected $isStretchWithOverflow;

    /**
     * @return bool
     */
    public function isStretchWithOverflow() {
        return $this->isStretchWithOverflow;
    }

    /**
     * @param bool $isStretchWithOverflow
     *
     * @return $this
     */
    public function setStretchWithOverflow($isStretchWithOverflow) {
        $this->isStretchWithOverflow = $isStretchWithOverflow;

        return $this;
    }
}
