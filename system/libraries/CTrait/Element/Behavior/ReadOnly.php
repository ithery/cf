<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:30:41 AM
 */
trait CTrait_Element_Behavior_ReadOnly {
    /**
     * @var bool
     */
    protected $readOnly;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setReadOnly($bool) {
        $this->readOnly = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReadOnly() {
        return $this->readOnly;
    }

    /**
     * @return bool
     */
    public function isReadOnly() {
        return $this->readOnly == true;
    }
}
