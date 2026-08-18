<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Behavior_Editable {
    /**
     * @var bool
     */
    protected $editable;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setEditable($bool = true) {
        $this->editable = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEditable() {
        return $this->editable;
    }

    /**
     * @return bool
     */
    public function isEditable() {
        return $this->editable == true;
    }
}
