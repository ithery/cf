<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:30:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Behavior_Disable {

    /**
     *
     * @var bool
     */
    protected $disable;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setDisable($bool) {
        $this->disable = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getDisable() {
        return $this->disable;
    }

    /**
     * 
     * @return bool
     */
    public function isDisable() {
        return $this->disable == true;
    }

}
