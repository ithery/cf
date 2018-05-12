<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:18:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Size {

    /**
     *
     * @var int 
     */
    protected $size;

    /**
     * 
     * @param int $size
     * @return $this
     */
    public function setSize($size) {

        $this->size = $size;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

}
