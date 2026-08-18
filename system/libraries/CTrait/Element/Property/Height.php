<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Property_Height {
    /**
     * @var int|string
     */
    protected $height;

    /**
     * @param int|string $height
     *
     * @return $this
     */
    public function setHeight($height) {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getHeight() {
        return $this->height;
    }
}
