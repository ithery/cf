<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:58:05 AM
 */
trait CTrait_Element_ActionList_Row {
    /**
     * @var null|CElement_List_ActionList
     */
    protected $rowActionList;

    /**
     * @return int
     */
    public function rowActionCount() {
        if ($this->rowActionList != null) {
            return $this->rowActionList->childCount();
        }

        return 0;
    }

    /**
     * @return bool
     */
    public function haveRowAction() {
        return $this->rowActionCount() > 0;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_ActionRow
     */
    public function addRowAction($id = '') {
        $rowAct = CElement_Component_ActionRow::factory($id);
        $this->getRowActionList()->add($rowAct);

        return $rowAct;
    }

    /**
     * @param string $style
     *
     * @return $this
     */
    public function setRowActionStyle($style) {
        $this->getRowActionList()->setStyle($style);

        return $this;
    }

    /**
     * @return string
     */
    public function getRowActionStyle() {
        return $this->getRowActionList()->getStyle();
    }

    /**
     * @return CElement_List_ActionRowList
     */
    public function getRowActionList() {
        if ($this->rowActionList == null) {
            $this->rowActionList = CElement_List_ActionRowList::factory();
        }

        return $this->rowActionList;
    }
}
