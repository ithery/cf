<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:55:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_Tab {

    /**
     * 
     * @param string $id
     * @return CElement_Component_Tab
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

}
