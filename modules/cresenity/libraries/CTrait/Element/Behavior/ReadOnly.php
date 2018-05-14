<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:30:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Behavior_ReadOnly {

    /**
     *
     * @var bool
     */
    protected $readOnly;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setReadOnly($bool) {
        $this->readOnly = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getReadOnly() {
        return $this->readOnly;
    }

    /**
     * 
     * @return bool
     */
    public function isReadOnly() {
        return $this->readOnly == true;
    }

}
