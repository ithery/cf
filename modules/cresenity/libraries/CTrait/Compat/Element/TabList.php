<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:55:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_TabList {

    /**
     * 
     * @param string $id
     * @return CElement_Component_TabList_Tab
     */
    public function add_tab($id = "") {
        return $this->addTab($id);
    }

}
