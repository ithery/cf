<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 7, 2018, 5:25:54 AM
 */
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
        $item = CElement_Factory::createComponent('Accordion_Item');
        $this->add($item);

        return $item;
    }
}
