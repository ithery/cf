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

    /**
     * 
     * @return int
     */
    public function rowActionCount() {
        if ($this->rowActionList != null) {
            return $this->rowActionList->childCount();
        }
        return 0;
    }

    /**
     * 
     * @return bool
     */
    public function haveRowAction() {
        return $this->rowActionCount() > 0;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Action
     */
    public function addRowAction($id = "") {
        $rowAct = CElement_Factory::createComponent('Action', $id);
        $this->rowActionList->add($rowAct);
        return $rowAct;
    }

    /**
     * 
     * @param string $style
     * @return $this
     */
    public function setRowActionStyle($style) {
        $this->rowActionList->setStyle($style);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRowActionStyle() {
        return $this->rowActionList->getStyle();
    }

    /**
     * 
     * @return CElement_List_ActionList
     */
    public function getRowActionList() {
        return $this->rowActionList;
    }

}
