<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:18:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Height {

    /**
     *
     * @var int 
     */
    protected $height;

    /**
     * 
     * @param int $width
     * @return $this
     */
    public function setHeight($height) {

        $this->height = $height;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

}
