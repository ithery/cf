<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:55:38 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_ActionList_Footer {

    /**
     *
     * @var CElement_List_ActionList
     */
    protected $footerActionList;

    /**
     * 
     * @return int
     */
    public function footerActionCount() {
        if ($this->footerActionList != null) {
            return $this->footerActionList->childCount();
        }
        return 0;
    }

    /**
     * 
     * @return boolean
     */
    public function haveFooterAction() {
        return $this->footerActionCount() > 0;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Action
     */
    public function addFooterAction($id = null) {
        $rowAct = $id;
        if (!($id instanceof CElement_Component_Action)) {
            $rowAct = CElement_Factory::createComponent('Action', $id);
        }


        $this->footerActionList->add($rowAct);
        return $rowAct;
    }

    /**
     * 
     * @param string $style
     * @return $this
     */
    public function setFooterActionStyle($style) {
        $this->footerActionList->setStyle($style);
        return $this;
    }

    /**
     * 
     * @return CElement_List_ActionList
     */
    public function getFooterActionList() {
        return $this->footerActionList;
    }

}
