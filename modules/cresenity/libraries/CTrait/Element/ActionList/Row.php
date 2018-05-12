<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:58:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_ActionList_Row {

    /**
     *
     * @var CElement_List_ActionList
     */
    protected $rowActionList;

    public function rowActionCount() {
        if ($this->rowActionList != null) {
            return $this->rowActionList->childCount();
        }
        return 0;
    }

    public function haveRowAction() {
        return $this->rowActionCount() > 0;
    }

    public function addRowAction($id = "") {
        $row_act = CElement_Factory::createComponent('Action', $id);
        $this->rowActionList->add($row_act);
        return $row_act;
    }

    public function setRowActionStyle($style) {
        $this->rowActionList->setStyle($style);
        return $this;
    }

    public function getRowActionStyle() {
        return $this->rowActionList->getStyle();
    }

    public function getRowActionList() {
        return $this->rowActionList;
    }

}
