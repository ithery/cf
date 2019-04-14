<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 7:22:25 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Trait_ListTrait {

    /**
     * 
     * 
     * @param string $id
     * @return CElement_List_ActionList
     */
    public function addActionList($id = "") {
        $actlist = CElement_Factory::createList('ActionList', $id);
        $this->add($actlist);
        if ($this instanceof CElement_Component_Form) {
            $actlist->setStyle('form-action');
        }
        return $actlist;
    }

    /**
     * 
     * @param string $id
     * @return CElement_List_TabList
     */
    public function addTabList($id = "") {
        $tabs = CElement_Factory::createList('TabList', $id);
        $this->add($tabs);
        return $tabs;
    }

}
