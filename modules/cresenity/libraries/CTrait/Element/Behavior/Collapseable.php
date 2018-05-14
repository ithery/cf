<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:30:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Behavior_Collapseable {

    /**
     *
     * @var bool
     */
    protected $collapseable;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setCollapseable($bool) {
        $this->collapseable = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getCollapseable() {
        return $this->collapseable;
    }

    /**
     * 
     * @return bool
     */
    public function isCollapseable() {
        return $this->collapseable == true;
    }

}
