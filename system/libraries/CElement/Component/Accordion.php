<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_Accordion extends CElement_Component {
    public function __construct($id) {
        parent::__construct($id);
        $this->addClass('component-accordion');
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Accordion_Item
     */
    public function addItem($id = null) {
        $item = new CElement_Component_Accordion_Item($id);
        $this->add($item);

        return $item;
    }
}
