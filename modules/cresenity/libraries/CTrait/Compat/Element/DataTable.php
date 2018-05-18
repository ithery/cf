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

    /**
     * 
     * @deprecated since version 1.2, please use setDataFromQuery
     * @return CElement_Component_DataTable
     */
    public function set_data_from_query($q) {
        return $this->setDataFromQuery($q);
    }

    /**
     * 
     * @deprecated since version 1.2, please use setAjax
     * @return CElement_Component_DataTable
     */
    public function set_ajax($bool) {
        return $this->setAjax($bool);
    }

    /**
     * 
     * @deprecated since version 1.2, please use rowActionCount
     * @return int
     */
    public function action_count() {
        return $this->rowActionCount();
    }

    /**
     * 
     * @deprecated since version 1.2, please use haveRowAction
     * @return bool
     */
    public function have_action() {
        return $this->haveRowAction();
    }

    /**
     * 
     * @deprecated since version 1.2, please use addRowAction
     * @return CElement_Component_Action
     */
    public function add_row_action($id = "") {
        return $this->addRowAction($id);
    }

    
    public function set_action_style($style) {
        return $this->setRowActionStyle($style);
       
    }
}
