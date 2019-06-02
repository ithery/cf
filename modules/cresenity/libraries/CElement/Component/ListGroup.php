<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 2, 2019, 9:50:41 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_ListGroup extends CElement_Component {

    use CTrait_Element_Property_Database,
        CTrait_Element_Property_TableData;

    protected $itemCallback = null;
    protected $itemCallbackRequire = '';

    public function __construct($id) {
        parent::__construct($id);
    }

    /**
     * 
     * @return CElement_Component_ListGroup_Item
     */
    public function addItem() {
        $item = CElement_Factory::createComponent('ListGroup_Item');
        $this->add($item);
        return $item;
    }

    public function setItemCallback($callback, $require = '') {
        $this->itemCallback = $callback;
        $this->itemCallbackRequire = $require;
        return $this;
    }

    public function build() {
        $this->addClass('list-group');
        $this->setAttr('role', 'tablist');
        if (is_array($this->tableData)) {
            foreach ($this->tableData as $rowData) {
                $item = $this->addItem()->setData($rowData);
                if ($this->itemCallback != null) {
                    $item->setCallback($this->itemCallback, $this->itemCallbackRequire);
                }
            }
        }
    }

}
