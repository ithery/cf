<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Property_Align {
    /**
     * @var string
     */
    protected $align;

    /**
     * @param string $align
     *
     * @return $this
     */
    public function setAlign($align) {
        $this->align = $align;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlign() {
        return $this->align;
    }
}
