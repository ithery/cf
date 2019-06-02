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
    public function addItem($id = "") {
        $item = CElement_Factory::createComponent('ListGroup_Item', $id);
        $this->add($item);
        return $item;
    }

    public function setItemCallback($callback, $require = '') {
        $this->itemCallback = CHelper::closure()->serializeClosure($callback);
        $this->itemCallbackRequire = $require;
        return $this;
    }
    
    public function getItemCallback() {
        return $this->itemCallback;
    }
    public function getItemCallbackRequire() {
        return $this->itemCallbackRequire;
    }
    
    public function setAjax($boolean=true) {
        $this->setTableDataIsAjax(true);
        return $this;
    }

    public function build() {
        $this->addClass('list-group');
        $this->setAttr('role', 'tablist');
        if (!$this->tableDataIsAjax) {
            $tableData= $this->getTableData();
            if (is_array($tableData)) {
                $index=0;
                foreach ($tableData as $rowData) {
                    $item = $this->addItem()->setData($rowData)->setIndex($index);
                    if ($this->itemCallback != null) {
                        $item->setCallback($this->itemCallback, $this->itemCallbackRequire);
                    }
                    $index++;
                }
            }
        }
    }

    public function js($indent = 0) {
        $js = '';
        if ($this->tableDataIsAjax) {

            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('ListGroup');
            $ajaxMethod->setData('owner', serialize($this));

            $ajaxUrl = $ajaxMethod->makeUrl();

            $ajaxOptions = array();
            $ajaxOptions['url'] = $ajaxUrl;
            $ajaxOptions['selector'] = '#'.$this->id;
            $js = 'cresenity.reload(' . json_encode($ajaxOptions) . ')';
        }
       
        return $js;
    }

}
