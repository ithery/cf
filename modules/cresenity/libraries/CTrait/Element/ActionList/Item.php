<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 2:46:55 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_ActionList_Item {

    /**
     *
     * @var CElement_List_ActionList
     */
    protected $itemActionList;

    /**
     * 
     * @return int
     */
    public function itemActionCount() {
        if ($this->itemActionList != null) {
            return $this->itemActionList->childCount();
        }
        return 0;
    }

    /**
     * 
     * @return bool
     */
    public function haveItemAction() {
        return $this->itemActionCount() > 0;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Action
     */
    public function addItemAction($id = "") {
        $item_act = CElement_Factory::createComponent('Action', $id);
        $this->itemActionList->add($item_act);
        return $item_act;
    }

    /**
     * 
     * @param string $style
     * @return $this
     */
    public function setItemActionStyle($style) {
        $this->itemActionList->setStyle($style);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getItemActionStyle() {
        return $this->itemActionList->getStyle();
    }

    /**
     * 
     * @return CElement_List_ActionList
     */
    public function getItemActionList() {
        return $this->itemActionList;
    }

}
