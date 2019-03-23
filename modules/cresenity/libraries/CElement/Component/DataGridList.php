<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 12:49:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_DataGridList extends CElement_Component {

    use CTrait_Element_ActionList_Row,
        CTrait_Element_Property_Title,
        CTrait_Element_ActionList_Header,
        CTrait_Element_DataProvider;

    /**
     *
     * @var CElement_Component_DataGridList_Header
     */
    protected $header;

    /**
     *
     * @var CElement_Element_Div
     */
    protected $content;

    /**
     *
     * @var array
     */
    protected $columns;
    protected $cellCallbackFunc;
    protected $filterActionCallbackFunc;
    protected $requires;
    protected $ajax;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->addClass('data-grid-list-box');
        $this->header = CElement_Factory::createComponent('DataGridList_Header');
        $this->add($this->header);
        $this->columns = array();
        $this->requires = array();
        $this->rowActionList = CElement_Factory::createList('ActionList');
        $this->rowActionList->setStyle('btn-icon-group');
        $this->headerActionList = CElement_Factory::createList('ActionList');
        $this->headerActionList->setStyle('widget-action');
        $this->content = $this->addDiv()->addClass('data-grid-list-container data-grid-list-col-view clearfix');
        $this->wrapper = $this->content;
    }

    public function build() {
        $divRowHeader = $this->addDiv()->addClass('data-grid-list-row-header');
        foreach ($this->columns as $column) {
            
        }
        $data = $this->getData();
        foreach ($data as $row) {
            $divRow = $this->addDiv()->addClass('data-grid-list-item');
            foreach ($row as $colKey => $colValue) {
                $divCol = $divRow->addDiv()->addClass('data-grid-list-item-col data-grid-list-item-col-' . $colKey);
                $divCol->add($colValue);
            }
        }
    }

    /**
     * 
     * @param string $fieldname
     * @return CElement_Component_DataGridList_Column
     */
    public function addColumn($fieldName) {
        $col = new CElement_Component_DataGridList_Column($fieldName);
        $this->columns[] = $col;
        return $col;
    }

    /**
     * Set callback for table cell render
     * 
     * @param callable $func parameter: $table,$col,$row,$value 
     * @param string $require File location of callable function to require
     * @return $this
     */
    public function cellCallbackFunc($func, $require = "") {
        $this->cellCallbackFunc = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function filterActionCallbackFunc($func, $require = "") {
        $this->filterActionCallbackFunc = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function setAjax($bool = true) {
        $this->ajax = $bool;
        return $this;
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();

        $js->append('
            
            
        ');



        $js->append($this->jsChild($indent))->br();
        return $js->text();
    }

}
