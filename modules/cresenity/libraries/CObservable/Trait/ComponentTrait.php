<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 8:18:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Trait_ComponentTrait {

    /**
     * 
     * @param string $id
     * @return CElement_Component_DataTable
     */
    public function addTable($id = "") {
        $table = CElement_Factory::createComponent('DataTable', $id);
        $this->add($table);
        return $table;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Nestable
     */
    public function addNestable($id = "") {
        $nestable = CElement_Factory::createComponent('Nestable', $id);
        $this->add($nestable);
        return $nestable;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Terminal
     */
    public function addTerminal($id = "") {
        $terminal = CElement_Factory::createComponent('Terminal', $id);
        $this->add($terminal);
        return $terminal;
    }

}
