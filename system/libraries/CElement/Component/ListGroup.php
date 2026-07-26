<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_ListGroup extends CElement_Component {
    use CTrait_Element_Property_Database,
        CTrait_Element_Property_TableData;

    /**
     * @var null|callable|CFunction_SerializableClosure
     */
    protected $itemCallback = null;

    /**
     * @var string
     */
    protected $itemCallbackRequire = '';

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_ListGroup_Item
     */
    public function addItem($id = '') {
        $item = new CElement_Component_ListGroup_Item($id);
        $this->add($item);

        return $item;
    }

    /**
     * @param callable $callback
     * @param string   $require
     *
     * @return $this
     */
    public function setItemCallback($callback, $require = '') {
        $this->itemCallback = c::toSerializableClosure($callback);
        $this->itemCallbackRequire = $require;

        return $this;
    }

    /**
     * @return null|callable|CFunction_SerializableClosure
     */
    public function getItemCallback() {
        return $this->itemCallback;
    }

    /**
     * @return string
     */
    public function getItemCallbackRequire() {
        return $this->itemCallbackRequire;
    }

    /**
     * @param bool $boolean
     *
     * @return $this
     */
    public function setAjax($boolean = true) {
        $this->setTableDataIsAjax(true);

        return $this;
    }

    /**
     * @return void
     */
    public function build() {
        $this->addClass('list-group');
        $this->setAttr('role', 'tablist');
        if (!$this->tableDataIsAjax) {
            $tableData = $this->getTableData();
            if (is_array($tableData)) {
                $index = 0;
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

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = '';
        if ($this->tableDataIsAjax) {
            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('ListGroup');
            $ajaxMethod->setData('owner', serialize($this));

            $ajaxUrl = $ajaxMethod->makeUrl();

            $ajaxOptions = [];
            $ajaxOptions['url'] = $ajaxUrl;
            $ajaxOptions['selector'] = '#' . $this->id;
            $js = 'cresenity.reload(' . json_encode($ajaxOptions) . ')';
        }
        $js .= parent::jsChild($indent);

        return $js;
    }
}
