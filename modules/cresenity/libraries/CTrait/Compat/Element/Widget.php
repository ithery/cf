<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:55:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_Widget {

    /**
     * 
     * @deprecated since version 1.2
     * @return type
     */
    public function set_title($title, $lang = true) {
        return $this->setTitle($title, $lang);
    }

    /**
     * 
     * @param type $icon
     * @return $this
     */
    public function set_icon($icon) {
        return $this->setIcon($icon);
    }

}
