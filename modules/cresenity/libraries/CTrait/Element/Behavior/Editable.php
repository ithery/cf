<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:30:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Behavior_Editable {

    /**
     *
     * @var bool
     */
    protected $editable;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setEditable($bool) {
        $this->editable = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getEditble() {
        return $this->editable;
    }

    /**
     * 
     * @return bool
     */
    public function isEditable() {
        return $this->editable == true;
    }

}
