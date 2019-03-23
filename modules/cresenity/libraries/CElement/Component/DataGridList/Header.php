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
        $this->addClass('bg-lightest mb-3 data-grid-list-header');
        $this->addHr();
        $this->content = $this->addDiv()->addClass('data-grid-list-actions container-p-x');
        $this->addHr();

        $this->addClass('data-grid-box');
        $this->wrapper = $this->content;
    }

    public function build() {
        $divActionPrimary = $this->addDiv()->addClass('grid-list-action-primary');
        $divActionSecondary = $this->addDiv()->addClass('grid-list-action-secondary');
        $divActionSecondaryInner = $divActionSecondary->addDiv()->addClass('btn-group btn-group-toggle')->setAttr('data-toggle', 'buttons');

        $colButtonLabel = $divActionSecondaryInner->addlabel()->addClass('btn btn-default icon-btn md-btn-flat')->addClass('active');
        $colButtonLabel->addControl($this->id . '_radio_col_view', 'radio')->setName($this->id . '-view')->addClass('data-grid-list-radio-view')->setValue('data-grid-list-col-view')->setChecked();
        $colButtonLabel->addSpan()->addClass('ion ion-md-apps');

        $rowButtonLabel = $divActionSecondaryInner->addlabel()->addClass('btn btn-default icon-btn md-btn-flat');
        $rowButtonLabel->addControl($this->id . '_radio_row_view', 'radio')->setName($this->id . '-view')->addClass('data-grid-list-radio-view')->setValue('data-grid-list-row-view');
        $rowButtonLabel->addSpan()->addClass('ion ion-md-menu');
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();

        $js->append("
            // Change view
            $('#".$this->id()." [name=\"".$this->id()."-view\"]').on('change', function() {
                $('#".$this->getParent()->id()." .data-grid-list-container')
                    .removeClass('data-grid-list-col-view data-grid-list-row-view')
                    .addClass(this.value);
            });

            
            ");



        $js->append($this->jsChild($indent))->br();
        return $js->text();
    }

}
