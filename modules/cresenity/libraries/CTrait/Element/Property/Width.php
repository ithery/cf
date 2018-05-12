<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:18:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Width {

    /**
     *
     * @var int 
     */
    protected $width;

    /**
     * 
     * @param int $width
     * @return $this
     */
    public function setWidth($width) {

        $this->width = $width;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

}
