<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:18:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Align {

    /**
     *
     * @var string
     */
    protected $align;

    /**
     * 
     * @param string $align
     * @return $this
     */
    public function setAlign($align) {

        $this->align = $align;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getAlign() {
        return $this->align;
    }

}
