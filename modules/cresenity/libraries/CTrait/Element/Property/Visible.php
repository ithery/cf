<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:36:51 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Visible {

    /**
     *
     * @var bool
     */
    protected $visible = true;

    public function setVisible($visible) {

        $this->visible = $visible;
        return $this;
    }

    public function getVisible() {
        return $this->visible;
    }

    public function isVisible() {
        return $this->visible == true;
    }

}
