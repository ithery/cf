<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:30:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Behavior_Scrollable {

    /**
     *
     * @var bool
     */
    protected $scrollable;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setScrollable($bool) {
        $this->scrollable = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getEditble() {
        return $this->scrollable;
    }

    /**
     * 
     * @return bool
     */
    public function isScrollable() {
        return $this->scrollable == true;
    }

}
