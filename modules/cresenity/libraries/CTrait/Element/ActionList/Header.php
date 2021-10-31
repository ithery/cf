<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:55:30 AM
 */
trait CTrait_Element_ActionList_Header {
    /**
     * @var CElement_List_ActionList
     */
    protected $headerActionList;

    /**
     * @return int
     */
    public function headerActionCount() {
        if ($this->headerActionList != null) {
            return $this->headerActionList->childCount();
        }
        return 0;
    }

    /**
     * @return bool
     */
    public function haveHeaderAction() {
        return $this->headerActionCount() > 0;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     */
    public function addHeaderAction($id = null) {
        $rowAct = $id;
        if (!($id instanceof CElement_Component_Action)) {
            $rowAct = CElement_Factory::createComponent('Action', $id);
        }
        $this->headerActionList->add($rowAct);
        return $rowAct;
    }

    /**
     * @param string $style
     *
     * @return $this
     */
    public function setHeaderActionStyle($style) {
        $this->headerActionList->setStyle($style);
        return $this;
    }

    /**
     * @return CElement_List_ActionList
     */
    public function getHeaderActionList() {
        return $this->headerActionList;
    }
}
