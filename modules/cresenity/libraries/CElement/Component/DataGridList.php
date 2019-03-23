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

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->header = CElement_Factory::createComponent('DataGridList_Header');
        $this->add($this->header);
    }

    public function build() {
        
    }

    
}
