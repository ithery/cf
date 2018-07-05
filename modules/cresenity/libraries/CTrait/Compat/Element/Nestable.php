<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 2:53:27 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_Nestable {

    /**
     * 
     * @deprecated since version 1.2
     * @param CTreeDB $treedb
     * @param int $parent_id
     * @return $this
     */
    public function set_data_from_treedb($treedb, $parent_id = null) {
        return $this->setDataFromTreeDb($treedb, $parent_id);
    }

    public function set_applyjs($boolean) {
        return $this->setApplyJs($boolean);
    }

    public function set_action_style($style) {
        return $this->setRowActionStyle($style);
    }

    public function action_count() {
        return $this->rowActionCount();
    }

    public function add_row_action($id = "") {
        return $this->addRowAction($id);
    }

    public function have_action() {
        return $this->haveRowAction();
    }

}
