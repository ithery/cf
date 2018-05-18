<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 12, 2018, 10:13:58 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_DataTable_Column {

    /**
     * 
     * @deprecated since version 1.2
     * @return $this
     */
    public function set_label($text, $lang = true) {
        return $this->setLabel($text, $lang);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @return string
     */
    public function get_label() {
        return $this->getLabel();
    }

}
