<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 1:24:32 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_DataGridList_Header extends CElement_Element {

    /**
     *
     * @var CElement_Element_Div
     */
    protected $content;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->addClass('bg-lightest container-m--x container-m--y mb-3');
        $this->addHr();
        $this->content = $this->addDiv()->addClass('container-p-x py-2');
        $this->addHr();

        $this->addClass('data-grid-box');
        $this->wrapper = $this->content;
    }

    public function build() {
        $divActionPrimary = $this->addDiv();
        $divActionSecondary = $this->addDiv();
        $divActionSecondaryInner = $divActionSecondary->addDiv()->addClass('btn-group btn-group-toggle')->setAttr('data-toggle', 'buttons');
    }

}
