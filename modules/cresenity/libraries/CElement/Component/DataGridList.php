<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 12:49:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_DataGridList extends CElement_Component {

    use CTrait_Element_ActionList_Row,
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

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->addClass('data-grid-list-box');
        $this->header = CElement_Factory::createComponent('DataGridList_Header');
        $this->add($this->header);
        $this->content = $this->addDiv()->addClass('data-grid-list-content clearfix');
        $this->wrapper = $this->content;
    }

    public function build() {
        $divRowHeader = $this->addClass('data-grid-content-header');
        
        $data = $this->getData();
        foreach ($data as $row) {
            $divRow = $this->addDiv()->addClass('data-grid-item');
            foreach ($row as $colKey => $colValue) {
                $divCol = $divRow->addDiv()->addClass('data-grid-item-col data-grid-item-col-'.$colKey);
                $divCol->add($colValue);
            }
        }
    }

}
