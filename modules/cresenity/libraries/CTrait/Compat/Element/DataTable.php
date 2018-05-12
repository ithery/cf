<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:55:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_DataTable {

    /**
     * 
     * @deprecated since version 1.2
     * @return CElement_Component_DataTable_Column
     */
    public function add_column($fieldname) {
        return $this->addColumn($fieldname);
    }

}
