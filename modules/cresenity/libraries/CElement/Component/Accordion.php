<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 5:25:54 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Accordion extends CElement_Component {

    /**
     *
     * @var string
     */
    protected $activeItemId;

    public function __construct($id) {
        parent::__construct($id);
        $this->addClass('component-accordion');
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Accordion_Item
     */
    public function addItem($id = null) {
        $item = CElement_Factory::createComponent('Accordion_Item');
        $this->add($item);
        if (strlen($this->activeItemId) == 0) {
            $this->activeItemId = $item->id;
        }
        return $item;
    }

}
