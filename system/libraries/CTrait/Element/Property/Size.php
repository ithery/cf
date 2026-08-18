<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Property_Size {
    /**
     * @var int
     */
    protected $size;

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size) {
        $this->size = $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize() {
        return $this->size;
    }
}
