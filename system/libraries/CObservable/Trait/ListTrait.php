<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CObservable_Trait_ListTrait {
    /**
     * @param string $id
     *
     * @return CElement_List_ActionList
     */
    public function addActionList($id = null) {
        $actlist = new CElement_List_ActionList($id);
        $this->wrapper->add($actlist);
        if ($this instanceof CElement_Component_Form) {
            $actlist->setStyle('form-action');
        }

        return $actlist;
    }

    /**
     * @param string $id
     *
     * @return CElement_List_TabList
     */
    public function addTabList($id = null) {
        $tabs = CElement_Factory::createList('TabList', $id);
        $this->add($tabs);

        return $tabs;
    }
}
